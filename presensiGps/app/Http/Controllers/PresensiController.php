<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    //
    public function create()
    {
        $hariini = date("Y-m-d");
        $nrp = Auth::guard('name')->user()->nrp;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nrp', $nrp)->count();
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $nrp = Auth::guard('name')->user()->nrp;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        //-6.2324736,106.8302336
        $latitudekantor = -6.2324736;
        $longitudekantor = 106.8302336;
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nrp', $nrp)->count();

        if($cek > 0){
            $ket = "out";
        }else{
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/uploads/absensi";
        $formatName = $nrp . "-" . $tgl_presensi . "-". $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if($radius > 20){
            echo "error|Anda Berada Diluar Radius " . $radius . " Meter dari kantor|radius";
        }else{
            if($cek > 0){
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi_out' => $lokasi
                ];
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nrp', $nrp)->update($data_pulang);
                if($update){
                    echo "success|Terima Kasih, Sampai Jumpa Esok Hari|out";
                    Storage::put($file, $image_base64);
                }else{
                    echo "error|Absen Gagal, Silahkan Coba Lagi Beberapa Saat atau Hubungi IT|out";
                }
            }else{
                $data = [
                    'nrp' => $nrp,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'lokasi_in' => $lokasi
                ];
                $simpan = DB::table('presensi')->insert($data);
                if($simpan){
                    echo "success|Terima Kasih, Selamat Bertugas|in";
                    Storage::put($file, $image_base64);
                }else{
                    echo "error|Absen Gagal, Silahkan Coba Lagi Beberapa Saat atau Hubungi IT|in";
                }
            }
        }
    }

    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
