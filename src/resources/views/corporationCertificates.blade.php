<div class="col-md-8">
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title">Corporation Certificates</h3>
        </div>
        <div class="box-body">
            <table id="corpCertTable" class="table table-hover" style="vertical-align: top">
                <thead>
                    <tr>
                    <th>Character</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('javascript')
    <script type="application/javascript">

        var corpCertTable = $('#corpCertTable').DataTable();
        $(function () {
            populateCorporationCertificates('{{auth()->user()->character->corporation_id}}');
        });

        function populateCorporationCertificates(corpID) {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcorpcert/" + corpID,
                type: "GET",
                dataType: 'json',
                timeout: 10000
            }).done(function (result) {
                if (result) {
                    $('#corpCertTable').find("tbody").empty();
                    if(corpCertTable){
                        corpCertTable.destroy();
                    };
                    headerPopulated = false;
                    for (var character in result) {
                        row ="<tr>";
                        row = row +"<td>" + result[character]['0'].character_name + "</td>";
                        for(var certificate in result[character]){
                            row = row +"<td>" + drawStars(result[character][certificate].rank) + "</td>";
                            row = row + "<td>" + (result[character][certificate].rank >4? 1:0) + "</td>";

                            // populate header and footer only once
                            if (!headerPopulated){
                                $('#corpCertTable').find("thead").find("tr").append( "<th>"+result[character][certificate].cert_name+"</th>");
                                $('#corpCertTable').find("thead").find("tr").append( "<th></th>");
                                $('#corpCertTable').find("tfoot").find("tr").append( "<td></td>");
                                $('#corpCertTable').find("tfoot").find("tr").append( "<td></td>");
                            }
                        }
                        row = row + "</tr>";

                        headerPopulated = true;
                        $('#corpCertTable').find("tbody").append(row);
                    }
                }

                corpCertTable = $('#corpCertTable').DataTable( {
                    "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;

                        // Remove the formatting to get integer data for summation
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
                        var populateFooters = function(col) {
                            // Total over all pages
                            total = api
                                .column(col)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);

                            // Total over this page
                            pageTotal = api
                                .column(col, {page: 'current'})
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);

                            // Update footer
                            $(api.column(col -1).footer()).html(
                                pageTotal + ' (' + total + ' total)'
                            );
                        }

                        for(i = 0; i < parseInt($('#corpCertTable thead th').length+2); i++) {
                            if (i > 0 && (i % 2 == 0)) {
                                populateFooters(i);
                                api.column(i).visible(false);
                            }
                        }
                    }
                } );
            });
            ids_to_names();
        }
    </script>
    @include('web::includes.javascript.id-to-name')
@endpush