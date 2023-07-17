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
        $tmp = "<h1>Votre Flotte</h1>
        <table>
        <tr>
            <td>Attaque</td>
            <td>défense</td>
            <td>Carburant</td>
            <td>Cout de production</td>
            <td>Temps de construction</td>
      
    </tr>";
        foreach ($this->fleet as $ship => $fleet) {
            $tmp .= "<tr><td><a href='/ships/$ship'>$ship</a></td><td>"
            .$ship ['attack']."</td><td>"
            .$ship ['defense']."</td><td>"
            .$ship ['fuel']."</td><td>"
            .$ship ['cost']."</td><td>"
            .$ship ['construction']."</td><td>"
            ."<form action='/fleet/$ship'method='post'>
            
        }

        $tmp .= "</table>";

        return $tmp;
    }