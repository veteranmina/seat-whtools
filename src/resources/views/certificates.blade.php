@extends('web::layouts.grids.8-4')

@section('title', trans('whtools::seat.certificates'))
@section('page_header', trans('whtools::seat.name'))
@section('page_description',trans('whtools::seat.certificates'))

@section('left')
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title">{{trans('whtools::whtools.certificateskills')}}</h3>
            @if (auth()->user()->has('whtools.certManager', false))
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-xs btn-box-tool" id="newCert" data-toggle="modal"
                            data-toggle="tooltip" data-target="#addCert" data-placement="top"
                            title="{{trans('whtools::whtools.createanewcertificate')}}">
                        <span class="fa fa-plus-square"></span>
                    </button>
                </div>
            @endif
        </div>
        <div class="box-body">
            <div class="input-group">
                <select id="certSpinner" class="form-control">
                    <option value="0">{{trans('whtools::whtools.choosecertificate')}}</option>
                    @foreach ($certificates as $cert)
                        <option value="{{ $cert['certID'] }}">{{ $cert['name'] }}</option>
                    @endforeach
                </select>
                <div class="input-group-btn">
                    @if ((auth()->user()->has('whtools.certManager', false)))

                        <button type="button" id="editCert" class="btn btn-warning" disabled="disabled" data-id=""
                                data-toggle="modal" data-target="#addCert" data-toggle="tooltip" data-placement="top"
                                title="Edit Cert" inactive>
                            <span class="fa fa-pencil text-white"></span>
                        </button>
                        <button type="button" id="deleteCert" class="btn btn-danger" disabled="disabled" data-id=""
                                data-toggle="tooltip" data-placement="top" title="Delete Cert">
                            <span class="fa fa-trash text-white"></span>
                        </button>
                    @endif
                </div>
            </div>
            <hr>
            <table id='skilllist' class="table table-hover" style="vertical-align: top">
                <thead>
                <tr>
                    <th></th>
                    <th>{{trans('web::seat.skill')}}</th>
                    <th>{{trans('whtools::whtools.requiredlevel')}}</th>
                    <th>{{trans('whtools::whtools.characterlevel')}}</th>
                    <th>{{trans('whtools::whtools.certificaterank')}}</th>
                    <th>{{trans('web::seat.status')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>


@endsection
@section('right')
    <div class="box box-primary box-solid" id="skills-box">
        <div class="box-header form-group"><h3 class="box-title" id="skill-title">
                My @if (auth()->user()->has('whtools.certchecker', false)) Corporation Members @endif Certificates</h3>
        </div>
        <div class="box-body">
            <div id="certificate-window">
                <select id="characterSpinner" class="form-control"></select>
                <table id="certificateTable" style="width: 100%" class="table table-condensed table-striped">
                    <thead>
                    <tr>
                        <th>{{trans('whtools::whtools.certificatename')}}</th>
                        <th style="width: 80px">{{trans('whtools::whtools.rank')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @include('whtools::includes.certificate-add')
    @include('whtools::includes.certificate-confirm-delete')
@endsection

@push('javascript')
    <script type="application/javascript">

        var certTable = $('#skilllist').DataTable();

        populateCharacterCertificates({{auth()->user()->character_id}});

        //rest spinner to default
        $('#certSpinner').val(0);

        $('#newCert').on('click', function () {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/skilllist",
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#listofskills').empty();
                $('#certificateID').val(0);
                $.each(result, function (key, value) {
                    $('#listofskills').append($("<option></option>").attr("value", value.typeID).text(value.typeName));
                });
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });
        });
        $('#listofskills').select2();
        $('#addSkills').on('click', function () {
            $("#listofskills option:selected").each(function () {

                var reqLvl = $("input[name='reqLvlList']:checked").val();
                var certRank = $("input[name='certLvlList']:checked").val();
                var skillCode = $(this).val() + reqLvl + certRank;
                $('#selectedSkills').append($("<option></option>").attr("value", skillCode).text($(this).text() + '     Lvl :' + reqLvl + '   Cert. Rank:' + certRank));
            });
        });

        $('#removeSkills').on('click', function () {
            $("#selectedSkills option:selected").each(function () {
                $('#selectedSkills option[value="' + $(this).val() + '"]').remove();
            });
        });

        $('#addCertForm').submit(function (event) {
            $('#selectedSkills').find("option").each(function () {
                $(this).prop('selected', true);
            });
        });

        $('#certSpinner').change(updateCertificateSkillList);

        $('#deleteCert').on('click', function () {
            $('#certConfirmModal').modal('show');
            $('#delSelection').val($(this).data('id'));
        });
        $('#deleteCertConfirm').on('click', function () {
            id = $('#certSpinner').find(":selected").val();

            $.ajax({
                headers: function () {
                },
                url: "/whtools/delcert/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#certSpinner option[value=' + id + ']').remove();
                $('#skilllist').find("tbody").empty();
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });
        });

        //ensure lvl number is last character used in comparitor
        function drawStars(lvl) {

            var stars = '';
            lvl = parseInt(lvl);
            if (lvl == 0) {
                stars = stars + '<i class="fa fa-star-o"></i>';
            } else if (lvl == 5) {
                stars = stars + '<span class="text-green">';
                for (var i = 1; i <= 5; i++) {
                    stars = stars + '<i class="fa fa-star"></i>';
                }
                stars.concat('</span>');
            } else {
                for (var i = 1; i <= lvl; i++) {
                    stars = stars + '<i class="fa fa-star"></i>';
                }
            }
            stars = stars + '| ' + lvl.toString();

            return stars;
        }

        $('#editCert').on('click', function () {
            var id = $('#certSpinner').find(":selected").val();

            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcertedit/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#listofskills').empty();
                $.each(result['allSkills'], function (key, value) {
                    $('#listofskills').append($("<option></option>").attr("value", value.typeID).text(value.typeName));
                });
                $('#selectedSkills').empty();
                $.each(result['certSkills'], function (key, value) {
                    $('#selectedSkills').append($("<option></option>").attr("value", value.skillID + String(value.requiredLvl) + String(value.certRank)).text(value.skillName + '     Lvl :' + value.requiredLvl + '   Cert. Rank:' + value.certRank));
                });
                $('#certificateID').val(result['cert']['certID']);
                $('#certificateName').val(result['cert']['name']);
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });

        });

        function updateCertificateSkillList() {
            var id = $('#certSpinner').find(":selected").val();
            if (id > 0) {
                $('button#editCert').prop('disabled', false);
                $('button#deleteCert').prop('disabled', false);

                $.ajax({
                    headers: function () {
                    },
                    url: "/whtools/getcertbyid/" + id,
                    type: "GET",
                    dataType: 'json',
                    timeout: 10000
                }).done(function (result) {
                    if (result) {
                        certTable.destroy();
                        $('#skilllist').find("tbody").empty();
                        rowNum = 1;
                        for (var skill in result) {

                            row = "<tr id='row" + rowNum + "'><td><img src='https://image.eveonline.com/Type/2403_32.png' height='24' /></td>";
                            row = row + "<td id='skillNameCell'>" + result[skill].skillName + "</td>";
                            row = row + "<td id='reqLvlCell' class='text-right'>" + drawStars(result[skill].reqLvl) + "</td>";
                            row = row + "<td id='charSkillCell' class='charSkill" + result[skill].skillID + " text-right'>Not Injected</td>";
                            row = row + "<td id='certRankCell' class='text-right'>" + drawStars(result[skill].certRank) + "</td>";
                            row = row + "<td id='statusCell'>Status</td>";
                            row = row + "</tr>";
                            $('#skilllist').find("tbody").append(row);
                            rowNum++;
                        }
                        updateCharacterTrained($('#characterSpinner').val());

                    }

                });
            } else {
                $('button#editCert').prop('disabled', true);
                $('button#deleteCert').prop('disabled', true);
            }
        }

        function updateCharacterTrained(characterID) {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcharskills/" + characterID,
                type: "GET",
                dataType: 'json',
                timeout: 10000
            }).done(function (result) {
                $.each(result, function (key, value) {
                    $('td.charSkill' + value.skill_id).html(drawStars(value.trained_skill_level));
                })
                $('#skilllist > tbody > tr').each(function (index, tr) {
                    currentRow = $(this);
                    reqLvlText = currentRow.find('#reqLvlCell').text();
                    reqLvl = reqLvlText.substr(reqLvlText.length - 1);
                    charSkillText = currentRow.find('#charSkillCell').text();
                    charSkill = charSkillText.substr(charSkillText.length - 1);
                    if (reqLvl <= charSkill) {
                        currentRow.find('#statusCell').html("Trained");
                    } else {
                        currentRow.find('#statusCell').html("Missing");
                    }


                });
                certTable = $('#skilllist').DataTable();
            });
        }

        function populateCharacterCertificates(characterID) {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcharcert/" + characterID,
                type: "GET",
                dataType: 'json',
                timeout: 10000
            }).done(function (result) {
                if (result) {
                    $('#certificateTable').find("tbody").empty();
                    for (var certificate in result) {
                        if (typeof (result[certificate]['characterCert']) !== "undefined") {
                            row = "<tr><td class='text-left'>" + result[certificate]['characterCert'].name + "</td>";
                            row = row + "<td class='text-right'>" + drawStars(result[certificate].certRank) + "</td>";
                            row = row + "</tr>";
                            $('#certificateTable').find("tbody").append(row);
                        } else if ($('#characterSpinner option').size() === 0) {
                            for (var toons in result[certificate].characters) {
                                $('#characterSpinner').append('<option value="' + result[certificate].characters[toons].character_id + '">' + result[certificate].characters[toons].name + '</option>');
                            }
                            $('#characterSpinner').select2();
                        }
                    }
                    $('#certSpinner').find('option value="0"').prop('selected', true);
                }

            });
        }

        $('#characterSpinner').change(function () {
            populateCharacterCertificates($('#characterSpinner').val());
            updateCertificateSkillList();
        });
    </script>
@endpush
