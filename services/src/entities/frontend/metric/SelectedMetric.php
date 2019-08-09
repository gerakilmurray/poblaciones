<?php

namespace helena\entities\frontend\metric;

use helena\entities\BaseMapModel;
use helena\classes\App;
use minga\framework\ErrorException;

class SelectedMetric extends BaseMapModel
{
	public $EllapsedMs;
	public $Cached = 0;

	public $SelectedVersionIndex;
	public $Metric;
	public $SummaryMetric = 'N';
	public $Transparency = 'M';

	public $Visible;
	public $ShowLegendsMetricName;


	// Posibles Valores:
	// - N (default): Cantidad absoluta.
	// - P: Porcentaje
	// - K: Area en KM2
	// - H: Area en Hect�reas (100x100m)
	// - A: Porcentaje del �rea total.
	// - D: Densidad (cantidad absoluta / km2)
	// valores posibles: 'U': Urbano denso, 'UD': Urbano total, 'R': Rural
	//									 'D': Urbano disperso, 'N': No indicado (todo)
	public $SelectedUrbanity = 'N';

	public $Versions = array();

	public static function GetMap()
	{
		return array ();
	}
	public function AddVersion($version)
	{
		$this->Versions[]= $version;
		$version->Metric = $this;
	}

	public function GetVersion($versionId)
	{
		foreach($this->Versions as $version)
			if ($version->Version->Id == $versionId)
				return $version;
		throw new ErrorException('La edici�n del indicador no ha sido encontrada.');
	}
}


