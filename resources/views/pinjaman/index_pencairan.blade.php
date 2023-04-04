@extends('layouts.web')

@section('contex')  
<div class="content">
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
        <li class="breadcrumb-item active">{{$menu}}</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">{{$menu}}&nbsp;&nbsp;<small>{{name_app()}}</small></h1>
    <div class="panel panel-success" data-sortable-id="ui-widget-11" >
        <div class="panel-heading">
            <h4 class="panel-title">Daftar Pencairan Pinjaman</h4>
            <div class="panel-heading-btn">
                <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a> -->
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <div class="panel-body">
            <div class="alert alert-yellow fade show m-b-10">
                <span class="close" data-dismiss="alert">×</span>
                <strong>Notifikasi!</strong> Daftar pengajuan yang akan dilakukan proses pencairan.
            </div>
            <div class="btn-group" style="margin-bottom:2%">
                <button type="button" class="btn btn-green btn-sm" onclick="download_transfer()"><i class="fa fa-download"></i> Download Pinjaman</button>
                <button type="button" class="btn btn-blue btn-sm" onclick="verifikasi_cair()"><i class="fa fa-cog"></i> Proses Pencairan</button>
               
            </div>
            <form method="post"   enctype="multipart/form-data" id="myalldata">
            @csrf
            <table id="datafixedheader" class="table table-striped table-bordered table-td-valign-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="5%"><input type="checkbox" id="pilihsemua"/></th>
                        <th width="7%">Nomor</th>
                        <th width="7%">NIK</th>
                        <th class="text-wrap">Name</th>
                        <th width="20%" class="text-nowrap">Nominal </th>
                        <th width="20%" class="text-nowrap">Nominal Transfer</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @foreach(get_pengajuan_cair() as $no=>$data)
                        <tr class="odd gradeX">
                            <td>{{$no+1}}</td>
                            <td><input type="checkbox" class="pilih"  name="id[]" value="{{$data->id}}"> </td>
                            <td>{{$data->nomorpinjaman}}</td>
                            <td>{{$data->nik}}</td>
                            <td>{{$data->user['name']}}</td>
                            <td>{{uang($data->nominal)}}</td>
                            <td>{{uang($data->nominal_cair)}}</td>
                            
                        </tr>
                    @endforeach
                    
                    
                </tbody>
            </table>
            </form>
            <br>
            <label>Telah dicairkan hari ini</label>
            <table id="datafixedheader" class="table table-striped table-bordered table-td-valign-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="7%">Nomor</th>
                        <th width="7%">NIK</th>
                        <th class="text-wrap">Name</th>
                        <th width="20%" class="text-nowrap">Nominal </th>
                        <th width="20%" class="text-nowrap">Nominal Transfer</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @foreach(get_pengajuan_barucair() as $no=>$data)
                        <tr class="odd gradeX">
                            <td>{{$no+1}}</td>
                            <td>{{$data->nomorpinjaman}}</td>
                            <td>{{$data->nik}}</td>
                            <td>{{$data->user['name']}}</td>
                            <td>{{uang($data->nominal)}}</td>
                            <td>{{uang($data->nominal_cair)}}</td>
                            
                        </tr>
                    @endforeach
                    
                    
                </tbody>
            </table>
        </div>
        
    </div>

    <div class="row">
        <div class="modal" id="modalapprove" aria-hidden="true" style="display: none;background: #1717198a;">
			<div class="modal-dialog" style="margin-top:0px">
				<div class="modal-content">
                    <div class="modal-header">
						<h4 class="modal-title">Approve Kontak Kerja</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body">
                        <div class="alert alert-yellow fade show m-b-10">
                            <span class="close" data-dismiss="alert">×</span>
                            <strong>Notifikasi!</strong> Masukan tanggal akhir kontrak untuk menetukan hasil verifikasi.
                        </div>
                        <div id="tampil-notifikasi"></div>
                        <form id="myapprove" method="post" enctype="multipart/form-data">
							@csrf
					        <div  id="tampil_approve" ></div>
                        </form>
					</div>
					
				</div>
			</div>
		</div>
        <div class="modal fade" id="modal-notifikasi" style="display: none;" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Notifikasi</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger m-b-0">
                            <h5><i class="fa fa-info-circle"></i> Erorr</h5>
                            <div id="isi-notifikasi"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Tutup</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('ajax')
    <script>
        var handleDataTableFixedHeader = function() {
            "use strict";
            
            if ($('#datafixedheader').length !== 0) {
                $('#datafixedheader').DataTable({
                    lengthMenu: [20, 40, 60],
                    fixedHeader: {
                        header: true,
                        headerOffset: $('#header').height()
                    },
                    responsive: false,
                    langth: false,
                    paging: false,
                    order: false,
                    info: false,
                });
            }
        };

        var TableManageFixedHeader = function () {
            "use strict";
            return {
                //main function
                init: function () {
                    handleDataTableFixedHeader();
                }
            };
        }();

        $(document).ready(function() {
            TableManageFixedHeader.init();
        });

        function download_transfer(){
            window.open("{{url('PinjamanTransfer/cetak')}}", "_blank");
        }
        $(function(){
    
            $("#pilihsemua").click(function () { 
                $(".pilih").attr("checked", this.checked);
            });
            $(".pilih").click(function(){
            
            if($(".pilih").length == $(".pilih:checked").length) {
                $("#pilihsemua").attr("checked", "checked");
            } else {
                $("#pilihsemua").removeAttr("checked");
            }
            
            });
        });

        function verifikasi_cair(){
            if (confirm('Apakah yakin ingin melakukan proses pencairan?')) {
                var form=document.getElementById('myalldata');
                
                $.ajax({
                    type: 'POST',
                    url: "{{url('/Pinjaman/verifikasi_cair')}}",
                    data: new FormData(form),
                    contentType: false,
                    cache: false,
                    processData:false,
                    beforeSend: function() {
						document.getElementById("loadnya").style.width = "100%";
					},
                    success: function(msg){
                        if(msg=='ok'){
                            location.reload();
                            
                        }else{
                            document.getElementById("loadnya").style.width = "0px";
                            $('#modal-notifikasi').modal('show')
                            $('#isi-notifikasi').html(msg);
                        }
                        
                        
                    }
                });
            }

        } 
    </script>

@endpush