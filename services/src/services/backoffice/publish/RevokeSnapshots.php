<?php

namespace helena\services\backoffice\publish;

use helena\services\common\BaseService;
use helena\db\admin\WorkModel;
use helena\classes\VersionUpdater;

use minga\framework\Profiling;
use minga\framework\Arr;

class RevokeSnapshots extends BaseService
{
	private $cacheManager;
	private $snapshotsManager;
	private $workId;
	private $shardifiedWorkId;
	private $workModel;
	private $work;
	private $publicWorkModel;
	private $publicWork;

	public function __construct($workId)
	{
		$this->cacheManager = new CacheManager();
		$this->snapshotsManager = new SnapshotsManager();
		$this->workId = $workId;
		$this->shardifiedWorkId = PublishDataTables::Shardified($workId);
		$this->workModel = new WorkModel();
		$this->work = $this->workModel->GetWork($workId);
		$this->publicWorkModel = new WorkModel(false);
		$this->publicWork = $this->publicWorkModel->GetWork($workId);
	}

	public function DeleteAllWorkDatasets()
	{
		$previousDatasets = PublishDataTables::UnshardifyList($this->publicWorkModel->GetDatasets($this->shardifiedWorkId), array('dat_id'));
		return $this->DeleteDatasets($previousDatasets);
	}
	public function DeleteMissingWorkDatasets()
	{
		$datasets = $this->workModel->GetDatasets($this->workId);
		$previousDatasets = PublishDataTables::UnshardifyList($this->publicWorkModel->GetDatasets($this->shardifiedWorkId), array('dat_id'));
		$removedDatasets = Arr::RemoveByField('dat_id', $previousDatasets, $datasets);
		if (sizeof($previousDatasets) != sizeof($datasets))
			$this->work['wrk_dataset_data_changed'] = true;
		return $this->DeleteDatasets($removedDatasets);
	}
	private function DeleteDatasets($datasetsToDelete)
	{
		Profiling::BeginTimer();

		if ($this->publicWork == null)
			// es la primera vez que se publica
			return;

		$datasets = $this->workModel->GetDatasets($this->workId);

		// Borra
		foreach($datasetsToDelete as $row)
		{
			$this->cacheManager->ClearDataset($row['dat_id']);
			$this->snapshotsManager->CleanDataset($row['dat_id']);
		}
		foreach(Arr::UniqueByField('dat_work_id', $datasetsToDelete) as $row)
		{
			$this->cacheManager->CleanMetadataPdfCache($row['dat_work_id']);
		}

		// Si hubo uso de datasets que antes no estaban o sac� alguno, tiene que regenerar
		if (sizeof($datasetsToDelete) > 0)
			$this->work['wrk_dataset_data_changed'] = true;

		// Actualiza metadatos
		if ($this->work['wrk_dataset_labels_changed'] || $this->work['wrk_dataset_data_changed'])
		{
			foreach($datasets as $row)
				$this->cacheManager->ClearDatasetMetaData($row['dat_id']);
			foreach($datasetsToDelete as $row)
				$this->cacheManager->ClearDatasetMetaData($row['dat_id']);

			foreach(Arr::UniqueByField('dat_work_id', $datasets) as $row)
				$this->cacheManager->CleanMetadataPdfCache($row);
			foreach(Arr::UniqueByField('dat_work_id', $datasetsToDelete) as $row)
				$this->cacheManager->CleanMetadataPdfCache($row);
		}

		Profiling::EndTimer();
	}

	public function DeleteAllWorkMetricVersions()
	{
		Profiling::BeginTimer();

		$metricVersions = $this->workModel->GetMetricVersions($this->workId);

		$previousMetricVersions = PublishDataTables::UnshardifyList($this->publicWorkModel->GetMetricVersions($this->shardifiedWorkId),
																																								array('mvr_id', 'mvr_metric_id'));

		// Limpia el fabCache
		if ($this->work['wrk_type'] === 'P')
		{
			VersionUpdater::Increment('FAB_METRICS');
			$this->cacheManager->CleanFabMetricsCache();
		}
		// Identifica qu� borrar
		$removedMetricVersions = $previousMetricVersions;

		// Borra
		$this->snapshotsManager->DeleteMetricVersionsByWork($this->workId);

		foreach($removedMetricVersions as $row)
		{
			$this->snapshotsManager->CleanMetricVersionData($row);
		}
		// Libera los metadatos del metric en el que est�n las versiones y de los borrados
		foreach(Arr::UniqueByField('mvr_metric_id', array_merge($previousMetricVersions, $metricVersions)) as $row)
		{
			$this->cacheManager->ClearMetricMetadata($row['mvr_metric_id']);
		}
	}
}

