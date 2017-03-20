<?php

namespace App\Http\Controllers;
use App\Post;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProcessgetdataController extends Controller{
	public function getData(){
		$url = 'https://landing.mapfre.com.pe/fondo-universitario/Default.aspx/getUserData';
		//data: '{ "strNumDoc":"' + $('#documento').val() + '" }',


		$client = new Client();
        $res = $client->request('POST', 'https://landing.mapfre.com.pe/fondo-universitario/Default.aspx/getUserData', ['strNumDoc' => '45624986']);

        $result= $res->getBody();
        dd($result);


/*
APE_MATERNO
APE_PATERNO
COD_DEPARTAMENTO
COD_DISTRITO
COD_INTERIOR
COD_NACIONALIDAD
COD_PROVINCIA
COD_SEXO
COD_TIP_DOMICILIO
COD_TIP_NUMERO
COD_ZONA
DES_DEPARTAMENTO
DES_DISTRITO
DES_PROVINCIA
DIRECCION
EMAIL
ESTADO
FEC_NACIMIENTO
MCA_ASEGURADO
MENSAJE
NOMBRES
NOM_INTERIOR
NOM_TIP_DOMICILIO
NOM_TIP_NUMERO
NOM_ZONA
NUM_DOCUMENTO
REFERENCIA
RESPUESTA
TELEFONO
TIP_DOCUMENTO
*/
	}

	public function testing(){
        $CountDischarges = '';
        $DuaList = fil();
        $count = 0;
        $RepeatsDUA = array(); 

        $ClientList = Client::where('RUC','>','0')->where('status', true)->distinct()->pluck('RUC')->toArray();
        $impList = $this->generateName();
        $fn = '';
        //dd($impList);
        \File::cleanDirectory('aduanadb');
        $zipper = new Zipper();
        for($i=0;$i<count($impList);$i++){
            $url[] = "http://www.aduanet.gob.pe/aduanas/informae/".$impList[$i].".zip";
            $filedir = "aduanadb/".$impList[$i].".zip";
            $fn = $impList[$i];
            if(file_exists($filedir)){
                echo($filedir.': file already exits'.'<br>' );
                //\File::delete($filedir);
                //\File::delete("aduanadb/".$impList[$i].'.DBF');
            }else{
                echo "Conectado a aduanet.gob.pe <br/>";
                echo($filedir.': file dont exits'.'<br>' );
                $zipFile = $filedir;//"aduabadb/".substr($url, -14, 10).".zip"; // Local Zip File Path
                $zipResource = fopen($zipFile, "w");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url[$i]);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 800);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                curl_setopt($ch, CURLOPT_FILE, $zipResource);
                $page = curl_exec($ch);
                if(!$page) {       
                    \File::cleanDirectory('aduanadb');
                    echo "Error :- ".curl_error($ch);
                }else{
                    echo('Descarga Completa'.'<br>');
                }
                $zipper->make($filedir)->extractTo('aduanadb');
                $fileUncompress = "aduanadb/".$impList[$i].'.DBF';
                if(file_exists($fileUncompress)){
                    echo $fileUncompress.'- Archivo Descomprimido Satisfactoriamente'.'<br>';
                }else{
                    echo $fileUncompress.'- La Descompresion fallo '.'<br><br>...................................';
                }
                curl_close($ch);
                $db=dbase_open($fileUncompress,2);
                if($db){
                    var_dump("DBF file $fileUncompress successfully opened <br/>");
                    $record_numbers = dbase_numrecords($db);
                    $CountCase = 0;
                    for ($i = 1; $i <= $record_numbers; $i++){
                        $tmp = array();
                        $forInsert = array();
                        $row = dbase_get_record_with_names($db, $i);
                        if(in_array($row["LIBR_TRIBU"], $ClientList)){
                            $CountCase++;
                            $tmp = [
                            "NUME_CORRE"    => $row["NUME_CORRE"],
                            "CODI_ADUAN"    => $row["CODI_ADUAN"],
                            "ANO_PRESE"     => $row["ANO_PRESE"],
                            "NUME_CORRE"    => $row["NUME_CORRE"],
                            "FECH_INGSI"    => verifyDate($row['FECH_INGSI']),
                            "TIPO_DOCUM"    => $row["TIPO_DOCUM"],
                            "LIBR_TRIBU"    => $row["LIBR_TRIBU"],
                            "DNOMBRE"       => mb_convert_encoding(trim($row["DNOMBRE"]), "UTF-8", "iso-8859-1"),
                            "CODI_AGENT"    => $row["CODI_AGENT"],
                            "FECH_LLEGA"    => verifyDate($row['FECH_LLEGA']),
                            "VIA_TRANSP"    => $row["VIA_TRANSP"],
                            "EMPR_TRANS"    => $row["EMPR_TRANS"],
                            "CODI_ALMA"     => $row["CODI_ALMA" ],
                            "CADU_MANIF"    => $row["CADU_MANIF"],
                            "FECH_MANIF"    => $row["FECH_MANIF"],
                            "NUME_MANIF"    => $row["NUME_MANIF"],
                            "FECH_RECEP"    => verifyDate($row['FECH_RECEP']),
                            "FECH_CANCE"    => verifyDate($row['FECH_CANCE']),
                            "TIPO_CANCE"    => $row["TIPO_CANCE"],
                            "BANC_CANCE"    => $row["BANC_CANCE"],
                            "CODI_ENFIN"    => $row["CODI_ENFIN"],
                            "DK"            => $row["DK"],
                            "PAIS_ORIGE"    => mb_convert_encoding(trim($row["PAIS_ORIGE"]), "UTF-8", "iso-8859-1"),
                            "PAIS_ADQUI"    => mb_convert_encoding(trim($row["PAIS_ADQUI"]), "UTF-8", "iso-8859-1"),
                            "PUER_EMBAR"    => trim($row["PUER_EMBAR"]),
                            "FECH_EMBAR"    => verifyDate($row["FECH_EMBAR"]),
                            "NUME_SERIE"    => trim($row["NUME_SERIE"]),
                            "PART_NANDI"    => $row["PART_NANDI"],
                            "DESC_COMER"    => mb_convert_encoding(trim($row["DESC_COMER"]), "UTF-8", "iso-8859-1"),
                            "DESC_MATCO"    => mb_convert_encoding(trim($row["DESC_MATCO"]), "UTF-8", "iso-8859-1"),
                            "DESC_USOAP"    => mb_convert_encoding(trim($row["DESC_USOAP"]), "UTF-8", "iso-8859-1"),
                            "DESC_FOPRE"    => mb_convert_encoding(trim($row["DESC_FOPRE"]), "UTF-8", "iso-8859-1"),
                            "DESC_OTROS"    => mb_convert_encoding(trim($row["DESC_OTROS"]), "UTF-8", "iso-8859-1"),
                            "FOB_DOLPOL"    => $row["FOB_DOLPOL"],
                            "FLE_DOLAR"     => $row["FLE_DOLAR"],
                            "SEG_DOLAR"     => $row["SEG_DOLAR"],
                            "PESO_NETO"     => $row["PESO_NETO"],
                            "PESO_BRUTO"    => $row["PESO_BRUTO"],
                            "UNID_FIQTY"    => $row["UNID_FIQTY"],
                            "UNID_FIDES"    => trim($row["UNID_FIDES"]),
                            "QUNICOM"       => $row["QUNICOM"],
                            "TUNICOM"       => trim($row["TUNICOM"]),
                            "SEST_MERCA"    => trim($row["SEST_MERCA"]),
                            "ADV_DOLAR"     => $row["ADV_DOLAR"],
                            "IGV_DOLAR"     => $row["IGV_DOLAR"],
                            "ISC_DOLAR"     => $row["ISC_DOLAR"],
                            "IPM_DOLAR"     => (float) trim($row["IPM_DOLAR"]),
                            "DES_DOLAR"     => $row["DES_DOLAR"],
                            "IPA_DOLAR"     => $row["IPA_DOLAR"],
                            "SAD_DOLAR"     => $row["SAD_DOLAR"],
                            "DER_ADUM"      => $row["DER_ADUM"],
                            "COMM"          => $row["COMM"],
                            "FMOD"          => verifyDate($row["FMOD"]),
                            "CANT_BULTO"    => $row["CANT_BULTO"],
                            "CLASE"         => $row["CLASE"],
                            "TRAT_PREFE"    => trim($row["TRAT_PREFE"]),
                            "TIPO_TRAT"     => $row["TIPO_TRAT"],
                            "CODI_LIBER"    => trim($row["CODI_LIBER"]),
                            "IMPR_RELIQ"    => trim($row["IMPR_RELIQ"]) ,
                            "deleted"       => $row["deleted"],
                            "created_at"    => date('Y-m-d H:i:s'),
                            "state"         => '1',
                            "status"        => true,
                            "created_by"    => 'SYSTEM'
                            ];
                            $forInsert[] = $tmp;
                            $forDischarge[] = $tmp;
                            Ma::insert($forInsert);
                            \File::cleanDirectory('aduanadb');
                        }
                    }
                    echo "se procesaron $CountCase registros <br/>";
                    echo 'se procesaron las importaciones del: '.substr($fn, 3, 8).'<br/>';
                    //echo 'se procesaron las importaciones del:'.date('d-m-Y', strtotime('-8 days')).' Al '.date('d-m-Y', strtotime('-2 days'));

                    //var_dump($forDischarge);

                    if(!empty($forDischarge) && count($forDischarge)){
                        foreach ($forDischarge as $Dischar) {
                            $conditional = (int) $Dischar["NUME_CORRE"];
                            if(in_array($conditional,$DuaList)){
                                array_push($RepeatsDUA, $conditional);
                            }else{
                                $tempt= array();
                                $DichargesInsertArr= array();
                                $PesoTotal = 0;
                                $Peso = 0;
                                $CantBultosTotal = 0;
                                $CantBultos = 0;
                                $SumaAseguradaTotal = 0;
                                $SumaAsegurada = 0;
                                $PrimaNetaTotal = 0;
                                $PrimaNeta = 0;
                                $hts_code_description = '';
                                $custom = '';
                                $manifiesto = '';
                                $importer = '';
                                $exporter = '';
                                $eta = '';
                                $loading_port = '';
                                $via = '';
                                $custom_agent = '';
                                $warehouse = '';
                                $RUC = 0;

                                $NUME_CORRE ='';
                                $CODI_ADUAN ='';
                                $ANO_PRESE      = '';
                                $NUME_CORRE     = '';
                                $FECH_INGSI     = '';
                                $TIPO_DOCUM     = '';
                                $LIBR_TRIBU     = '';
                                $DNOMBRE        = '';
                                $CODI_AGENT     = '';
                                $FECH_LLEGA     = '';
                                $VIA_TRANSP     = '';
                                $EMPR_TRANS     = '';
                                $CODI_ALMA      = '';
                                $CADU_MANIF     = '';
                                $FECH_MANIF     = '';
                                $NUME_MANIF     = '';
                                $FECH_RECEP     = '';
                                $FECH_CANCE     = '';
                                $TIPO_CANCE     = '';
                                $BANC_CANCE     = '';
                                $CODI_ENFIN     = '';
                                $DK             = '';
                                $PAIS_ORIGE     = '';
                                $PAIS_ADQUI     = '';
                                $PUER_EMBAR     = '';
                                $FECH_EMBAR     = '';
                                $NUME_SERIE     = '';
                                $PART_NANDI     = '';
                                $DESC_COMER     = '';
                                $DESC_MATCO     = '';
                                $DESC_USOAP     = '';
                                $DESC_FOPRE     = '';
                                $DESC_OTROS     = '';
                                $FOB_DOLPOL     = '';
                                $FLE_DOLAR      = '';
                                $SEG_DOLAR      = '';
                                $PESO_NETO      = '';
                                $PESO_BRUTO     = '';
                                $UNID_FIQTY     = '';
                                $UNID_FIDES     = '';
                                $QUNICOM        = '';
                                $TUNICOM        = '';
                                $SEST_MERCA     = '';
                                $ADV_DOLAR      = '';
                                $IGV_DOLAR      = '';
                                $ISC_DOLAR      = '';
                                $IPM_DOLAR      = '';
                                $DES_DOLAR      = '';
                                $IPA_DOLAR      = '';
                                $SAD_DOLAR      = '';
                                $DER_ADUM       = '';
                                $COMM           = '';
                                $FMOD           = '';
                                $CANT_BULTO     = '';
                                $CLASE          = '';
                                $TRAT_PREFE     = '';
                                $TIPO_TRAT      = '';
                                $CODI_LIBER     = '';
                                $IMPR_RELIQ     = '';
                                foreach ($forDischarge as $key => $value) {
                                    if($value["NUME_CORRE"] == $conditional){
                                        //$Peso = $value->gross_kg;
                                        $Peso = $value["PESO_BRUTO"];
                                        $PesoTotal = $PesoTotal + $Peso;
                                        //$CantBultos = (int) $value->qty_1;
                                        $CantBultos = (int) $value["UNID_FIQTY"];
                                        $CantBultosTotal = $CantBultosTotal + $CantBultos;
                                        //$SumaAsegurada = $value->u_cfr_tot;
                                        $SumaAsegurada = $value["FOB_DOLPOL"] + $value["FLE_DOLAR"];
                                        $SumaAseguradaTotal = $SumaAseguradaTotal + $SumaAsegurada;
                                        //$PrimaNeta = $value->u_insurance_tot;
                                        $PrimaNeta = $value["SEG_DOLAR"];
                                        $PrimaNetaTotal = $PrimaNetaTotal + $PrimaNeta;
                                        
                                        $RUC = (int) $value["LIBR_TRIBU"];
                                        $hts_code_description = $value["DESC_COMER"];
                                        $custom_code = $value["CODI_ADUAN"];
                                        $manifiesto = $value["CADU_MANIF"].'-'.$value["FECH_MANIF"].'-'.$value["NUME_MANIF"];
                                        $importer = $value["DNOMBRE"];
                                        //$exporter = $value->exporter;
                                        $eta = $value["FECH_LLEGA"];
                                        $loading_port_code = $value["PUER_EMBAR"];
                                        $via_code = $value["VIA_TRANSP"];
                                        $custom_agent_code = $value["CODI_AGENT"];
                                        $warehouse_code = $value["CODI_ALMA"];

                                        $NUME_CORRE         =$value["NUME_CORRE"];
                                        $CODI_ADUAN         =$value["CODI_ADUAN"];
                                        $ANO_PRESE          =$value["ANO_PRESE"];
                                        $NUME_CORRE         =$value["NUME_CORRE"];
                                        $FECH_INGSI         =$value["FECH_INGSI"];
                                        $TIPO_DOCUM         =$value["TIPO_DOCUM"];
                                        $LIBR_TRIBU         =$value["LIBR_TRIBU"];
                                        $DNOMBRE            =$value["DNOMBRE"];
                                        $CODI_AGENT         =$value["CODI_AGENT"];
                                        $FECH_LLEGA         =$value["FECH_LLEGA"];
                                        $VIA_TRANSP         =$value["VIA_TRANSP"];
                                        $EMPR_TRANS         =$value["EMPR_TRANS"];
                                        $CODI_ALMA          =$value["CODI_ALMA"];
                                        $CADU_MANIF         =$value["CADU_MANIF"];
                                        $FECH_MANIF         =$value["FECH_MANIF"];
                                        $NUME_MANIF         =$value["NUME_MANIF"];
                                        $FECH_RECEP         =$value["FECH_RECEP"];
                                        $FECH_CANCE         =$value["FECH_CANCE"];
                                        $TIPO_CANCE         =$value["TIPO_CANCE"];
                                        $BANC_CANCE         =$value["BANC_CANCE"];
                                        $CODI_ENFIN         =$value["CODI_ENFIN"];
                                        $DK                 =$value["DK"];
                                        $PAIS_ORIGE         =$value["PAIS_ORIGE"];
                                        $PAIS_ADQUI         =$value["PAIS_ADQUI"];
                                        $PUER_EMBAR         =$value["PUER_EMBAR"];
                                        $FECH_EMBAR         =$value["FECH_EMBAR"];
                                        $NUME_SERIE         =$value["NUME_SERIE"];
                                        $PART_NANDI         =$value["PART_NANDI"];
                                        $DESC_COMER         =$value["DESC_COMER"];
                                        $DESC_MATCO         =$value["DESC_MATCO"];
                                        $DESC_USOAP         =$value["DESC_USOAP"];
                                        $DESC_FOPRE         =$value["DESC_FOPRE"];
                                        $DESC_OTROS         =$value["DESC_OTROS"];
                                        $FOB_DOLPOL         =$value["FOB_DOLPOL"];
                                        $FLE_DOLAR          =$value["FLE_DOLAR"];
                                        $SEG_DOLAR          =$value["SEG_DOLAR"];
                                        $PESO_NETO          =$value["PESO_NETO"];
                                        $PESO_BRUTO         =$value["PESO_BRUTO"];
                                        $UNID_FIQTY         =$value["UNID_FIQTY"];
                                        $UNID_FIDES         =$value["UNID_FIDES"];
                                        $QUNICOM            =$value["QUNICOM"];
                                        $TUNICOM            =$value["TUNICOM"];
                                        $SEST_MERCA         =$value["SEST_MERCA"];
                                        $ADV_DOLAR          =$value["ADV_DOLAR"];
                                        $IGV_DOLAR          =$value["IGV_DOLAR"];
                                        $ISC_DOLAR          =$value["ISC_DOLAR"];
                                        $IPM_DOLAR          =$value["IPM_DOLAR"];
                                        $DES_DOLAR          =$value["DES_DOLAR"];
                                        $IPA_DOLAR          =$value["IPA_DOLAR"];
                                        $SAD_DOLAR          =$value["SAD_DOLAR"];
                                        $DER_ADUM           =$value["DER_ADUM"];
                                        $COMM               =$value["COMM"];
                                        $FMOD               =$value["FMOD"];
                                        $CANT_BULTO         =$value["CANT_BULTO"];
                                        $CLASE              =$value["CLASE"];
                                        $TRAT_PREFE         =$value["TRAT_PREFE"];
                                        $TIPO_TRAT          =$value["TIPO_TRAT"];
                                        $CODI_LIBER         =$value["CODI_LIBER"];
                                        $IMPR_RELIQ         =$value["IMPR_RELIQ"];
                                        $deleted            =$value["deleted"];




                                    }
                                }
                                $tempt = [
                                        'Mercaderia'        => $hts_code_description,
                                        'NumeroDUA'         => $conditional,
                                        'RUC'               => $RUC,

                                        'Manifiesto'        => $manifiesto,
                                        'Consignatario'     => $importer,
                                        //'Proveedor'         => $exporter,
                                        'FechaLlegada'      => $eta,

                                        'Peso'              => $PesoTotal,
                                        'CantBultos'        => $CantBultosTotal,
                                        'SumaAsegurada'     => $SumaAseguradaTotal,
                                        'PrimaNeta'         => $PrimaNetaTotal,

                                        //'LugarEmbarque'     => $loading_port,
                                        //'TipoTransporte'    => $via,
                                        //'NombreAgencia'     => $custom_agent,
                                        //'Almacen'           => $warehouse,
                                        //'Aduanas'     => $custom,

                                        'CodigoAduanas'         => $custom_code,
                                        'CodigoPuertoEmbarque'  => $loading_port_code,
                                        'CodigoViaTransporte'   => $via_code,
                                        'CodigoAgente'          => $custom_agent_code,
                                        'CodigoAlmacen'         => $warehouse_code,
                                        'Estado'            => 'IMPORTADO',
                                        'created_at'        => date('Y-m-d H:i:s'),

                                        'NUME_CORRE'        => $NUME_CORRE,
                                        'CODI_ADUAN'        => $CODI_ADUAN,
                                        'ANO_PRESE'         => $ANO_PRESE,
                                        'NUME_CORRE'        => $NUME_CORRE,
                                        'FECH_INGSI'        => $FECH_INGSI,
                                        'TIPO_DOCUM'        => $TIPO_DOCUM,
                                        'LIBR_TRIBU'        => $LIBR_TRIBU,
                                        'DNOMBRE'           => $DNOMBRE,
                                        'CODI_AGENT'        => $CODI_AGENT,
                                        'FECH_LLEGA'        => $FECH_LLEGA,
                                        'VIA_TRANSP'        => $VIA_TRANSP,
                                        'EMPR_TRANS'        => $EMPR_TRANS,
                                        'CODI_ALMA'         => $CODI_ALMA,
                                        'CADU_MANIF'        => $CADU_MANIF,
                                        'FECH_MANIF'        => $FECH_MANIF,
                                        'NUME_MANIF'        => $NUME_MANIF,
                                        'FECH_RECEP'        => $FECH_RECEP,
                                        'FECH_CANCE'        => $FECH_CANCE,
                                        'TIPO_CANCE'        => $TIPO_CANCE,
                                        'BANC_CANCE'        => $BANC_CANCE,
                                        'CODI_ENFIN'        => $CODI_ENFIN,
                                        'DK'                => $DK,
                                        'PAIS_ORIGE'        => $PAIS_ORIGE,
                                        'PAIS_ADQUI'        => $PAIS_ADQUI,
                                        'PUER_EMBAR'        => $PUER_EMBAR,
                                        'FECH_EMBAR'        => $FECH_EMBAR,
                                        'NUME_SERIE'        => $NUME_SERIE,
                                        'PART_NANDI'        => $PART_NANDI,
                                        'DESC_COMER'        => $DESC_COMER,
                                        'DESC_MATCO'        => $DESC_MATCO,
                                        'DESC_USOAP'        => $DESC_USOAP,
                                        'DESC_FOPRE'        => $DESC_FOPRE,
                                        'DESC_OTROS'        => $DESC_OTROS,
                                        'FOB_DOLPOL'        => $FOB_DOLPOL,
                                        'FLE_DOLAR'         => $FLE_DOLAR,
                                        'SEG_DOLAR'         => $SEG_DOLAR,
                                        'PESO_NETO'         => $PESO_NETO,
                                        'PESO_BRUTO'        => $PESO_BRUTO,
                                        'UNID_FIQTY'        => $UNID_FIQTY,
                                        'UNID_FIDES'        => $UNID_FIDES,
                                        'QUNICOM'           => $QUNICOM,
                                        'TUNICOM'           => $TUNICOM,
                                        'SEST_MERCA'        => $SEST_MERCA,
                                        'ADV_DOLAR'         => $ADV_DOLAR,
                                        'IGV_DOLAR'         => $IGV_DOLAR,
                                        'ISC_DOLAR'         => $ISC_DOLAR,
                                        'IPM_DOLAR'         => $IPM_DOLAR,
                                        'DES_DOLAR'         => $DES_DOLAR,
                                        'IPA_DOLAR'         => $IPA_DOLAR,
                                        'SAD_DOLAR'         => $SAD_DOLAR,
                                        'DER_ADUM'          => $DER_ADUM,
                                        'COMM'              => $COMM,
                                        'FMOD'              => $FMOD,
                                        'CANT_BULTO'        => $CANT_BULTO,
                                        'CLASE'             => $CLASE,
                                        'TRAT_PREFE'        => $TRAT_PREFE,
                                        'TIPO_TRAT'         => $TIPO_TRAT,
                                        'CODI_LIBER'        => $CODI_LIBER,
                                        'IMPR_RELIQ'        => $IMPR_RELIQ,
                                        'deleted'           => $deleted
                                    ];
                                    array_push($DuaList, $conditional);
                                    $count++;
                                    $DichargesInsertArr[] = $tempt;
                                    
                                    Discharge::insert($DichargesInsertArr);
                            }
                        }
                        //dd($DichargesInsertArr);
                        echo "Se Registraron $count Descargas";
                    }
                }
            }
        }
    }

	/*public function __construct(){
		$this->middleware('auth')->except(['index','show']);
	}
	
	public function index(){
		$posts = Post::latest()->get();
		return view('posts.index', compact('posts'));
	}
	public function show(Post $post){
		return view('posts.show', compact('post'));
	}
	public function create(){
		return view('posts.create');
	}
	public function store(){
		$this->validate(request(),[
			'title' => 'required|max:30',
			'body'	=> 'required'
			]);
		Post::create(request(['title','body']));
		return redirect('/');
	}*/
	

}
