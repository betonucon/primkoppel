<html>
  <head>
    <title>Daftar Pinjaman Transfer</title>
    <style>
      html{
        margin:0% 0% 0% 0%;
      }
      h3{
        margin:0px;
      }
      h5{
        margin:0px;
      }
      .atas{
        padding:2%;
        width:100%;
        background:aqua;
        height:65px;
        text-align:center;

      }
      .body{
        padding:2%;
        width:100%;
      }
      table{
        width:100%;
        
        border-collapse:collapse;
      }
      .tth{
        font-size:13px;
        background:aqua;
        border:solid 1px #000;
      }
      .ttd{
        font-size:13px;
        padding-left:4px;
        border:solid 1px #000;
      }
    </style>
  </head>
  <body>
    <div class="atas">
      <h3>DAFTAR TRANSFER PINJAMAN </h3>
      <h5>KOPERASI MITRA SEJAHTERA</h5>
      <h5>Tahun: {{date('Y')}} Bulan: {{date('F')}}</h5>
    </div>
    <div class="body">
            <table width="100%">
                <thead>
                    <tr>
                        <th class="tth" width="5%">No</th>
                        <th class="tth" width="10%">Nomor</th>
                        <th class="tth" width="10%">NIK</th>
                        <th class="tth" >Name</th>
                        <th class="tth" width="20%">Nominal </th>
                        <th class="tth" width="20%">Nominal Transfer</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @foreach($data as $no=>$data)
                        <tr>
                            <td class="ttd">{{$no+1}}</td>
                            <td class="ttd">{{$data->nomorpinjaman}}</td>
                            <td class="ttd">{{$data->nik}}</td>
                            <td class="ttd">{{$data->user['name']}}</td>
                            <td class="ttd">{{uang($data->nominal)}}</td>
                            <td class="ttd">{{uang($data->nominal_cair)}}</td>
                            
                        </tr>
                    @endforeach
                    
                    
                </tbody>
            </table>
    </div>
  </body>
</html>

