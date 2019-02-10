<?php
/*
This file is part of SeAT

Copyright (C) 2015, 2017  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace FlyingFerret\Seat\WHTools\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Denngarr\Seat\Fitting\Http\Controllers\FittingController;

use Seat\Services\Repositories\Character\Info;
use Seat\Services\Repositories\Character\Skills;
use Seat\Ser7vices\Repositories\Configuration\UserRespository;


use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

use Denngarr\Seat\Fitting\Helpers\CalculateConstants;
use Denngarr\Seat\Fitting\Helpers\CalculateEft;
use Denngarr\Seat\Fitting\Models\Fitting;
use Denngarr\Seat\Fitting\Models\Doctrine;
use Denngarr\Seat\Fitting\Models\Sde\InvType;
use Denngarr\Seat\Fitting\Models\Sde\DgmTypeAttributes;
use Denngarr\Seat\Fitting\Validation\FittingValidation;
use Denngarr\Seat\Fitting\Validation\DoctrineValidation;

use Seat\Eveapi\Models\Contracts\CorporationContract;
use Seat\Eveapi\Models\Contracts\ContractDetail;
use FlyingFerret\Seat\WHTools\Models\Stocklvl;
use FlyingFerret\Seat\WHTools\Validation\StocklvlValidation;
                                     

/**
 * Class HomeController
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class WHtoolsController extends FittingController
{

    /**
     * @return \Illuminate\View\View
     */
    public function getHome()
    {

        return view('whtools::whtools');
    }
    public function getFittingView()
    {
        $corps = [];
        $stock = $this->getStockList();
        
        
        
        $fitlist = $this->getFittingList();
        
        return view('whtools::stocking', compact('fitlist', 'stock','contracts'));
    }
    
    public function getStockLvls(){
        return Stocklvl::all();
    }
        
    
    public function getStockList(){
        $stocklvllist = $this->getStockLvls();
        $stock = [];
        
        if(count($stocklvllist)<= 0)
            return $stock;
        
        foreach($stocklvllist as $stocklvl){
            $ship = InvType::where('typeName', $stocklvl->fitting->shiptype)->first();

            $corporation_id = auth()->user()->character->corporation_id;
            
            $stock_contracts = [];
           
            $stock_contracts = ContractDetail::where('issuer_corporation_id','=',$corporation_id)
                ->where('title', 'LIKE', '%'.$stocklvl->fitting->shiptype.' '.$stocklvl->fitting->fitname.'%')
                ->where('for_corporation', '=', '1')
                ->where('status','LIKE','outstanding')
                ->get();
            
            array_push($stock, [
                'id' =>  $stocklvl->id,
                'minlvl' =>  $stocklvl->minLvl,
                'stock' =>  count($stock_contracts),
                'fitting_id' =>  $stocklvl ->fitting_id,
                'fitname' => $stocklvl->fitting->fitname,
                'shiptype' =>$stocklvl->fitting->shiptype,
                'typeID' => $ship->typeID
            ]);
        }
        return $stock;
        
        
    }
    
    public function saveStocking(StocklvlValidation $request){
        $stocklvl = Stocklvl::firstOrNew(['fitting_id' =>$request->selectedfit]);

        $stocklvl->minLvl = $request->minlvl;
        $stocklvl->fitting_id = $request->selectedfit;
        $stocklvl->save();
        
        $stock = $this->getStockList();
        $fitlist = $this->getFittingList();
        
        return view('whtools::stocking', compact('fitlist', 'stock'));
    }
    
        public function deleteStockingById($id)
    {
        Stocklvl::destroy($id);
        
        return "Success";
    }

}
