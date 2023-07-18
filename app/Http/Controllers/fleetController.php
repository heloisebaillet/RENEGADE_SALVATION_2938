<?php


namespace App\Http\Controllers;

use Faker\Core\Number;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Nop;
use App\Models\Cruiser;
use App\Models\Destroyer;

class fleetController extends Controller
{
    private $fleet = [
        'Chasseur' => ['attack' => '11','defense'=> '7','fuel'=> '1/10','cost'=> '50','construction'=> '1h','image'=> 'chasseur.jpg'],
        'Fregate'=> ['attack' => '13','defense'=> '5','fuel'=> '2/10','cost'=> '200','construction'=> '2h', 'image'=>'fregate.jpg'],
        'Cruiser' => ['attack' => '14','defense'=> '9','fuel'=> '4/10','cost'=> '800','construction'=> '4h', 'image'=>'croiseur.jpg'],
        'Destroyer' => ['attack' => '27','defense'=> '20','fuel'=> '8/10','cost'=> '2000','construction'=> '8h','image'=>'destroyer'],
    ];

    public function index()
    {
       

     

      
    
    }
public function create (Request $request, $type = null){
    if($type == "Cruiser" || $type == "Destroyer" || $type == "chasseur" || $type == "fregate"){
        
        if ($type == "cruiser")
            $fuel_consumption ="";
            $cruiser = new Cruiser();
            $cruiser->user_id = $user_id;
            $cruiser->type = $type;
            return Response()->json($cruiser, );}

}

        