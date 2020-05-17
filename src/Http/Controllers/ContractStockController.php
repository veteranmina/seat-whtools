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

namespace veteranmina\Seat\ContractStock\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Denngarr\Seat\Fitting\Http\Controllers\FittingController;


use Denngarr\Seat\Fitting\Models\Fitting;
use Denngarr\Seat\Fitting\Models\Sde\InvType;

use Seat\Eveapi\Models\Contracts\ContractDetail;
use veteranmina\Seat\ContractStock\Models\Stocklvl;
use veteranmina\Seat\ContractStock\Validation\StocklvlValidation;

use Seat\Eveapi\Models\Wallet\CharacterWalletTransaction;
use Seat\Web\Models\User;

use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Seat\Eveapi\Models\Character\CharacterInfo;

use DateTime;
use GuzzleHttp\Client;
use Parsedown;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Wallet\CorporationWalletJournal;
use Seat\Eveapi\Models\Universe\UniverseName;

/**
 * Class HomeController
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class ContractStockController extends FittingController
{

    /**
     * @return \Illuminate\View\View
     */
    public function getHome()
    {

        return view('contractstock::contractstock');
    }

    public function getStockingView()
    {
        $stock = $this->getStockList();

        $fitlist = $this->getFittingList();

        return view('contractstock::stocking', compact('fitlist', 'stock'));
    }

    public function getStockList()
    {
        $stocklvllist = Stocklvl::all();
        $stock = [];

        if ($stocklvllist->isEmpty())
            return $stock;

        //Hard code corps (Yes I know I shouldnt...)
        //$corporation_id = auth()->user()->character->corporation_id;
		$contract_corp_id = '98502642'
		$main_corp_id = '98293472'

        foreach ($stocklvllist as $stocklvl) {
            $ship = InvType::where('typeName', $stocklvl->fitting->shiptype)->first();

            //Contracts made to the corp but by corp members not on behalf of the corp
            $member_stock_contracts = ContractDetail::where('issuer_corporation_id', '=', $corporation_id)
                ->where('title', 'LIKE', '%' . trim($stocklvl->fitting->fitname) . '%')
                ->where('for_corporation', '=', '0')
                ->where('status', 'LIKE', 'outstanding')
                ->get();
            //Contracts made to the corp but by Pospy corp members not on behalf of the corp
            $member_stock_contracts = ContractDetail::where('issuer_corporation_id', '=', $main_corp_id)
                ->where('title', 'LIKE', '%' . trim($stocklvl->fitting->fitname) . '%')
                ->where('for_corporation', '=', '0')
                ->where('status', 'LIKE', 'outstanding')
                ->get();
            //Contracts made to the corp by corp members on behalf of the corp
            $stock_contracts = ContractDetail::where('issuer_corporation_id', '=', $corporation_id)
                ->where('title', 'LIKE', '%' . trim($stocklvl->fitting->fitname) . '%')
                ->where('for_corporation', '=', '1')
                ->where('status', 'LIKE', 'outstanding')
                ->get();
            //Contracts made to Pospy corp by Contract corp members on behalf of the corp
            $stock_contracts = ContractDetail::where('issuer_corporation_id', '=', $contract_corp_id)
                ->where('title', 'LIKE', '%' . trim($stocklvl->fitting->fitname) . '%')
                ->where('for_corporation', '=', '1')
                ->where('status', 'LIKE', 'outstanding')
                ->get();

            $totalContractsValue = 0;

            foreach ($stock_contracts as $contract) {
                $totalContractsValue += $contract->price;
            }

            array_push($stock, [
                'id' => $stocklvl->id,
                'minlvl' => $stocklvl->minLvl,
                'stock' => $stock_contracts->count(),
                'members_stock' => $member_stock_contracts->count(),
                'fitting_id' => $stocklvl->fitting_id,
                'fitname' => $stocklvl->fitting->fitname,
                'shiptype' => $stocklvl->fitting->shiptype,
                'typeID' => $ship->typeID,
                'totalContractsValue' => $totalContractsValue
            ]);
        }
        return $stock;


    }

    public function saveStocking(StocklvlValidation $request)
    {
        $stocklvl = Stocklvl::firstOrNew(['fitting_id' => $request->selectedfit]);

        $stocklvl->minLvl = $request->minlvl;
        $stocklvl->fitting_id = $request->selectedfit;
        $stocklvl->save();

        $stock = $this->getStockList();
        $fitlist = $this->getFittingList();

        return view('contractstock::stocking', compact('fitlist', 'stock'));
    }

    public function deleteStockingById($id)
    {
        Stocklvl::destroy($id);

        return "Success";
    }

    public function getConfigView()
    {
        $changelog = $this->getChangelog();
        $corporationsInfo = CorporationInfo::all();
        $corps = [];

        foreach ($corporationsInfo as $c) {
            array_push($corps, [
                'name' => $c->name,
                'id' => $c->corporation_id
            ]);
        }

        return view('contractstock::config', compact('changelog', 'corps'));
    }

    private function getChangelog(): string
    {
        try {
            $response = (new Client())
                ->request('GET', "https://raw.githubusercontent.com/veteranmina/contractstock/master/CHANGELOG.md");
            if ($response->getStatusCode() != 200) {
                return 'Error while fetching changelog';
            }
            $parser = new Parsedown();
            return $parser->parse($response->getBody());
        } catch (RequestException $e) {
            return 'Error while fetching changelog';
        }
    }

    /*add validation*/
    public function postConfig()
    {
        setting(['contractstock.corp.corptomem', request('contractstock-corp-corptomem')], true);
        setting(['contractstock.corp.memtocorp', request('contractstock.corp.memtocorp')], true);

        return redirect()->route('contractstock.config');
    }
}
