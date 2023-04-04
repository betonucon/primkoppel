<div class="row">
	<div class="col-md-12">
		<div class="widget widget-stats bg-blue" style="background:#eaf2f9 !important;color: #171336;">
			<div class="stats-icon" style="color: #171336;"><i class="fa fa-desktop"></i></div>
			<div class="stats-info"  >
				<h4 style="color: #171336;">JUMLAH TERDAFTAR SEBAGAI ANGGOTA AKTIF SAAT INI</h4>
				<p>{{total_anggota()}} Anggota</p>	
			</div>
			
		</div>
		<input type="hidden" name="id" value="{{$id}}">
		<div id="error-notif"></div>
                                
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label>NIK</label>
			<input type="text" class="form-control" {{$read}} placeholder="Ketik disini.." value="{{$data->nik}}" name="username">
		</div>
		<div class="form-group">
			<label>Nama</label>
			<input type="text" class="form-control" placeholder="Ketik disini.." name="name" value="{{$data->nama}}">
		</div>
		<div class="form-group">
			<label>Alamat</label>
			<textarea class="form-control" rows="4" placeholder="Ketik disini.." name="alamat">{{$data->alamat}}</textarea>
		</div>
		<div class="form-group">
			<label>Email</label>
			<input type="text" class="form-control" placeholder="Ketik disini.." name="email" value="{{$data->email}}">
		</div>
	</div>
	<div class="col-md-6">
		
		
		<div class="form-group">
			<label>No Handphone</label>
			<input type="text" class="form-control" placeholder="Ketik disini.." name="no_hp" value="{{$data->no_hp}}">
		</div>
		<div class="form-group">
			<label>Tanggal Masuk</label>
			<input type="text" class="form-control" placeholder="Ketik disini.." id="tanggalindo" name="tgl_masuk" value="{{$data->masuk}}">
		</div>
		<div class="form-group">
			<label>Status Kenggotaan</label>
			<select placeholder="Ketik disini.." class="form-control" name="status">
				@foreach(get_status() as $sts)
					<option value="{{$sts->id}}" @if($sts->id==$data->status) selected @endif >{{$sts->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label>Nominal Simpanan Wajib</label>
			<input type="text" class="form-control" placeholder="Ketik disini.." onkeypress="return hanyaAngka(event)" name="nominal" value="{{$data->nominal}}">
		</div>
	</div>
	
</div>

<script>
		$('#tanggalindo').datepicker({
			format: 'yyyy-mm-dd',
		});
</script>