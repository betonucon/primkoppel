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
            <h4 class="panel-title">Pengajuan Pinjaman</h4>
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
                <strong>Notifikasi!</strong> Verifikasi pengajuan peminjaman dana.
            </div>
            <table id="datafixedheader" class="table table-striped table-bordered table-td-valign-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-wrap">Anggota</th>
                        <th width="10%" class="text-nowrap">Nominal</th>
                        <th width="10%" class="text-nowrap">Cicilan /bulan</th>
                        <th width="12" class="text-nowrap">Waktu</th>
                        <th width="8%" class="text-nowrap">Bunga /blan</th>
                        <th width="8%" class="text-nowrap">Total Bunga</th>
                        <th width="10%" class="text-nowrap">Tgl Pengajuan</th>
                        <th class="text-nowrap" width="10%">Act</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @foreach(get_pengajuan() as $no=>$data)
                        <tr class="odd gradeX">
                            <td>{{$no+1}}</td>
                            <td>Akun:{{$data->nik}}<br>Nama: {{$data->user['name']}}</td>
                            <td>{{uang($data->nominal)}}</td>
                            <td>{{uang($data->cicilan)}}</td>
                            <td>{{$data->waktu}} Bulan</td>
                            <td>{{uang($data->bunga)}}</td>
                            <td>{{uang($data->total_bunga)}}</td>
                            <td>{{$data->tgl_pengajuan}}</td>
                            <td>
                                <span onclick="approve({{$data->id}})" class="btn btn-purple active btn-xs"><i class="fas fa-check-circle fa-sm"></i>Proses</span> 
                            </td>
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
						<h4 class="modal-title">Approve Masa Pinjaman</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body">
                        <div class="alert alert-yellow fade show m-b-10">
                            <span class="close" data-dismiss="alert">×</span>
                            <strong>Notifikasi!</strong> Pemeriksaan pinjaman sebelumnya.
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
                    paging: true,
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

        function approve(id){
			$.ajax({
				type: 'GET',
				url: "{{url('Pinjaman/approve')}}",
				data: "id="+id,
				success: function(msg){
					$('#modalapprove').modal('show');
					$('#tampil_approve').html(msg);
				}
			});
			
		}

        function verifikasi_pinjaman(){
            
            var form=document.getElementById('myapprove');
            var token= "{{csrf_token()}}";
                $.ajax({
                    type: 'POST',
                    url: "{{url('/Pinjaman/approve')}}?_token="+token,
                    data: new FormData(form),
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(msg){
                        if(msg=='ok'){
                            location.reload();
                               
                        }else{
                            $('#tampil-notifikasi').html(msg);
                        }
                        
                        
                    }
                });

        } 
    </script>

@endpush