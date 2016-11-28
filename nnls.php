<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('serverConnect.php');
global $conn;

require_once 'Math/Matrix.php';

/*
Initialize:
Set P = null
Set R = {1, ..., n}
Set x to an all-zero vector of dimension n
Set w = A_t(y − Ax)
*/
echo nl2br("INITIALIZE--------------------------------------\n");
$A = array(
	array(50.0,5.0,0.0,0.0),
	array(2.0,27.0,1.0,20.0),
	array(4.0,1.0,11.0,3.0),
); //food macros
$A = new Math_Matrix($A);
$A_t = $A->cloneMatrix();
$A_t->transpose(); //Transpose of A
$size = $A->getSize();
$m = $size[0]; //rows
$n = $size[1];//columns

$y = array(
	array(50.0),
	array(45.0),
	array(25.0),
); //target macros
$y = new Math_Matrix($y);
$P= [];
$R = [];
$x_arr = [];
for($i=0; $i<$n; $i++)
{
	$R[$i] = 1;
	$P[$i] = 0;
	$x_arr[$i] = array(0);
}

$x = new Math_Matrix($x_arr);
echo nl2br("A:\n".$A -> toString()."rows: ".$m."\n columns: ".$n."\n\nA_t:\n".$A_t->toString()."\n y:\n".$y->toString()."\n x:\n".$x->toString());
$t = 0;
$w = 0;
echo nl2br("\n w:");
set_w();
$AP = new Math_Matrix($data=null);
$AP_t = new Math_Matrix($data=null);
$s = new Math_Matrix($x_arr);
$sP = new Math_Matrix($data=null);
echo nl2br("\n s:\n".$s->toString());
		



nnls();
/*
Inputs:
a real-valued matrix A of dimension m × n
a real-valued vector y of dimension m
a real value t, the tolerance for the stopping criterion
*/
function nnls(){
	global $t, $w, $P, $R, $AP, $s, $sP, $x; 
	while($R != null && $w->getMax() > $t)
	{
		echo nl2br("\nLOOP---------------------------------------------------------\n");
		$j = $w->getMaxIndex();
		$j = $j[0];
		echo nl2br("Max Index of w: ".$j);
		$P[$j] = 1;
		echo nl2br("\nP:\n");
		echo implode(", ", $P);
		unset($R[$j]);
		echo nl2br("\nR:\n");
		echo implode(", ", $R);
		set_AP();
		set_s();
		echo nl2br($sP->getMin()."\n Inner Loop if vlaue above <= zero\n");
		/*while($sP->getMin() <= 0)
		{
			echo nl2br("\nINNER LOOP--------------------------------------------------\n");
			$i = $sP->getSize();
			$i = $i[0];
			$g =1;
			for($k=0; $k<$i; $k++)
			{
				$b = $sP->getElement($k, 0);
				echo $b."<br>";
				if( $b <= 0)
				{
					$c = $x->getElement($k,0);
					echo $c."<br>";
					$b = $c - $b;
					echo $b."<br>";
					$b = $b/$c;
					echo $b."<br>";
					if($b<$g)
					{
						$g = $b;
					}
				}
			}
			$i = $s->cloneMatrix();
			$i->sub($x);
			$i->scale($g);
			$x->add($i);
			$i = $x->getSize();
			$i = $i[0];
			for($k=0; $k<$i; $k++)
			{
				if($x->getElement($k,0) == 0)
				{
					array_push($R, 1);
				}
			}
			set_s();
		}*/
		$x = $s->cloneMatrix();
		echo nl2br("\nx: \n".$x->toString());
		set_w();
	}
	echo nl2br("\nx: \n".$x->toString());

}

function set_w()
{
	global $A, $w, $x, $y, $A_t;
	echo nl2br("\nSet w to A_t(y − Ax)\n\n");
	$w = $A->cloneMatrix();
	echo nl2br("Step1: Ax\nA:\n".$w->toString()." multiply \nx:\n".$x->toString()." Ax equals:\n");
	$w->multiply($x);
	echo nl2br($w->toString());
	echo nl2br("\nStep 2: y - Ax\ny:\n".$y->toString()."minus\nAx:\n".$w->toString()."y - Ax equals:\n");
	$y->sub($w);
	echo nl2br($y->toString());
	$w = $A_t->cloneMatrix();
	echo nl2br("\nStep 3: A_t(y − Ax)\n A_t:\n".$w->toString()."multiply\n(y − Ax):\n".$y->toString()."A_t(y − Ax) equals: \n");
	$w->multiply($y);
	echo nl2br("w:\n".$w->toString());
}

function set_AP()
{
	global $P,$A, $AP, $AP_t;
	$k = 0;
	$j = [];
	for($i=0; $i<count($P); $i++)
	{
		if($P[$i] != 0)
		{
			$j[$k] = $A->getCol($i);
			$k++;
		}
	}
	if($j != null)
	{
		$AP_t = new Math_Matrix($j);
		$AP = $AP_t->cloneMatrix();
		$AP->transpose();
		
	}
	echo nl2br("\nAP:\n".$AP->toString()."\nAP_t:\n".$AP_t->toString());
}

function set_s()
{
	global $AP_t, $AP, $y, $sP, $P, $s, $x_arr;
		echo nl2br("\nSet sP = (AP_t AP)^-1 (AP_t)y\n");
		$sP = $AP_t->cloneMatrix();
		$sP->multiply($AP);
		echo nl2br("\nAP_t:\n".$AP_t->toString()."multpily\nAP:\n".$AP->toString()."AP_t AP equals:\n".$sP->toString());
		$sP->invert();
		echo nl2br("(AP_t AP)^-1 equals:\n".$sP->toString());
		$sP->multiply($AP_t);
		echo nl2br("AP_t AP)^-1 (AP_t) equals:\n".$sP->toString());
		$sP->multiply($y);
		echo nl2br("AP_t AP)^-1 (AP_t)y equals:\nsP:\n".$sP->toString());
		$k=0;
		$l=0;
		for($i=0; $i<count($P); $i++)
		{
			if($P[$i] != null)
			{
				$s->setRow($i, $sP->getRow($k));
				$k++;
			}
			else
			{
				$s->setRow($i, $x_arr[$l]);
				$l++;
			}
		}
		echo nl2br("s:\n".$s->toString());
}

?>