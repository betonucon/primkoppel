@extends('layouts.web')
@push('style')
	<link href="{{url('assets/assets/plugins/nvd3/build/nv.d3.css')}}" rel="stylesheet" />
@endpush
@section('contex')
   

	<div id="content" class="content">
		<div class="profile">
			<div class="profile-header">
				<div class="profile-header-cover"></div>
				<div class="profile-header-content">
					<div class="profile-header-img">
						<img src="{{url('img/kopkar.png')}}" alt="">
					</div>
					<div class="profile-header-info">
						<h4 class="mt-0 mb-1">{{aplikasi()}}</h4>
						<p class="mb-2">SALDO MASUK {{date('Y')}} : Rp.{{uang(masuk_transaksi(date('Y')))}}</p>
						<p class="mb-2">SALDO KELUAR {{date('Y')}} : Rp.{{uang(keluar_transaksi(date('Y')))}}</p>
						<p class="mb-2">SALDO {{date('Y')}} : Rp.{{uang(masuk_transaksi(date('Y'))-keluar_transaksi(date('Y')))}}</p>
						
						<!-- <a href="#" class="btn btn-xs btn-yellow">Edit Profile</a> -->
					</div>
				</div>
				
			</div>
		</div>  
	</div>
	<div id="content" class="content">
			
			<div class="row">
				
				<div class="col-xl-4 col-lg-6">
					<!-- begin card -->
					<div class="card border-0 mb-3 bg-dark-darker text-white">
						<!-- begin card-body -->
						<div class="card-body" style="background: no-repeat bottom right; background-image: url(../assets/img/svg/img-4.svg); background-size: auto 60%;">
							<!-- begin title -->
							<div class="mb-3 text-grey">
								<b>KEUANGAN {{$tahun}}</b>
								<span class="text-grey ml-2"><i class="fa fa-info-circle" data-toggle="popover" data-trigger="hover" data-title="Sales by social source" data-placement="top" data-content="Total online store sales that came from a social referrer source." data-original-title="" title=""></i></span>
							</div>
							<!-- end title -->
							<!-- begin sales -->
							<h3 class="m-b-10">Rp.<span data-animation="number" data-value="{{sum_transaksi_tahunan($tahun,1)}}">{{sum_transaksi_tahunan($tahun,1)}}</span></h3>
							
						</div>
						<!-- end card-body -->
						<!-- begin widget-list -->
						<div class="widget-list widget-list-rounded inverse-mode">
							@for($x=1;$x<13;$x++)
				
			
								<a href="#" class="widget-list-item rounded-0 p-t-3">
									<div class="widget-list-content">
										<div class="widget-list-title">{{bulan(ubah_bulan($x))}}</div>
									</div>
									<div class="widget-list-action text-nowrap text-grey">
										Rp<span data-animation="number" data-value="{{sum_transaksi($tahun,ubah_bulan($x),1)}}">{{sum_transaksi($tahun,ubah_bulan($x),1)}}</span>
									</div>
								</a>
							@endfor
						</div>
						<!-- end widget-list -->
					</div>
					<!-- end card -->
				</div>
				
				<div class="col-xl-8 ui-sortable">
					
				
					<!-- begin panel -->
					<div class="panel panel-inverse" data-sortable-id="chart-js-2">
						<div class="panel-heading ui-sortable-handle">
							<h4 class="panel-title">Bar Chart</h4>
							<div class="panel-heading-btn">
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
							</div>
						</div>
						<div class="panel-body">
							<p>
								Grafik Keuangan {{name_app()}} Tahun {{$tahun}}
							</p>
							<div><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
								<canvas id="bar-chart" data-render="chart-js" width="495" height="247" style="display: block; width: 495px; height: 247px;" class="chartjs-render-monitor"></canvas>
							</div>
						</div>
					</div>
					<!-- begin panel -->
					<div class="panel panel-inverse" data-sortable-id="chart-js-2">
						<div class="panel-heading ui-sortable-handle">
							<h4 class="panel-title">Bar Chart</h4>
							<div class="panel-heading-btn">
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
								<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
							</div>
						</div>
						<div class="panel-body">
							<p>
								Grafik Keuangan {{name_app()}} Tahun {{$tahun}}
							</p>
							
						</div>
					</div>
				</div>
				
			</div>
			

		</div>
	
	
@endsection
@push('ajax')
<script src="{{url('assets/assets/plugins/chart.js/dist/Chart.min.js')}}"></script>
<script>
    $("#ClickMe").attr("disabled", "disabled"); 

	/*
	Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 4
	Version: 4.6.0
	Author: Sean Ngu
	Website: http://www.seantheme.com/color-admin/admin/
	*/

	Chart.defaults.global.defaultFontColor = COLOR_DARK;
	Chart.defaults.global.defaultFontFamily = FONT_FAMILY;
	Chart.defaults.global.defaultFontStyle = FONT_WEIGHT;

	var randomScalingFactor = function() { 
		return Math.round(Math.random()*100)
	};

	
	var barChartData = {
		labels: [
			@for($x=1;$x<13;$x++)
				'{{bulan(ubah_bulan($x))}}',
			@endfor
		],
		datasets: [{
			label: 'Masuk',
			borderWidth: 2,
			borderColor: COLOR_INDIGO,
			backgroundColor:"blue",
			data: [
				@for($x=1;$x<13;$x++)
					{{sum_transaksi($tahun,ubah_bulan($x),1)}},
				@endfor
			]
		}, {
			label: 'Keluar',
			borderWidth: 2,
			borderColor: COLOR_DARK,
			backgroundColor: "red",
			data: [
				@for($x=1;$x<13;$x++)
					{{sum_transaksi($tahun,ubah_bulan($x),2)}},
				@endfor
			]
		}]
	};

	

	var handleChartJs = function() {
		var ctx2 = document.getElementById('bar-chart').getContext('2d');
		var barChart = new Chart(ctx2, {
			type: 'bar',
			data: barChartData
		});

		
	};

	var ChartJs = function () {
		"use strict";
		return {
			//main function
			init: function () {
				handleChartJs();
			}
		};
	}();

	$(document).ready(function() {
		ChartJs.init();
	});
</script>
@endpush
