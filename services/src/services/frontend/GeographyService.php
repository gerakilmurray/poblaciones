<?php

namespace helena\services\frontend;

use helena\classes\App;
use helena\classes\GeoJson;
use helena\classes\GlobalTimer;
use helena\caches\GeographyCache;

use helena\services\common\BaseService;
use helena\db\frontend\SnapshotGeographyItemModel;
use helena\db\frontend\GeographyModel;
use helena\entities\frontend\clipping\FeaturesInfo;
use helena\entities\frontend\geometries\Envelope;


class GeographyService extends BaseService
{
	// Los niveles de zoom se mapean con la calidad de imagen
	// de modo que CALIDAD = Max(5, ((int)((zoom + 2) / 3))),
	// es decir que z[1 a 3] = C1, z[4 a 6] = C2, m�ximo C5.
	private const PAGE_SIZE = 25000;

	public function GetGeography($geographyId, $x, $y, $z, $b, $page = 0)
	{
		$data = null;
		$key = GeographyCache::CreateKey($x, $y, $z, $b, $page);
		if (GeographyCache::Cache()->HasData($geographyId, $key, $data))
		{
			return $this->GotFromCache($data);
		}

		$data = $this->CalculateGeography($geographyId, $x, $y, $z, $b, $page);

		GeographyCache::Cache()->PutData($geographyId, $key, $data);

		return $data;
	}

	private function CalculateGeography($geographyId, $x, $y, $z, $b, $page)
	{
		// calcula los GeoData (seg�n indicado por campo 'resumen' en el ABM)
		// para cada categor�a del metric indicado en la regi�n clipeada.
		$table = new SnapshotGeographyItemModel();

		if ($b != null)
		{
			$envelope = Envelope::TextDeserialize($b);
		}
		else
		{
			$envelope = Envelope::FromXYZ($x, $y, $z);
		}

		$cartoTable = new GeographyModel();
		$carto = $cartoTable->GetGeographyInfo($geographyId);
		$getCentroids = ($carto['geo_min_zoom'] == null || $z >= $carto['geo_min_zoom']);

		$rows = $table->GetGeographyByEnvelope($geographyId, $envelope, $z, $getCentroids);

		$project = false; // $z <= 17;
		$totalPages = ceil(sizeof($rows) / self::PAGE_SIZE);
		if ($totalPages > 1)
		{
			$rows = array_slice($rows, $page * self::PAGE_SIZE, self::PAGE_SIZE);
		}
		$data = FeaturesInfo::FromRows($rows, $getCentroids, $project);
		if ($totalPages > 0)
		{
			$data->Page = $page;
			$data->TotalPages = $totalPages;
		}
		return $data;
	}
}

