<?php

namespace helena\services\backoffice\publish\snapshots;

use helena\services\backoffice\publish\PublishDataTables;
use minga\framework\Profiling;
use helena\classes\App;

class SnapshotMetricVersionModel
{
	public function RegenMetric($metricId)
	{
		$metricIdShardified = PublishDataTables::Shardified($metricId);

	 	Profiling::BeginTimer();
		$sql = "INSERT INTO snapshot_metric_versions ( mvw_metric_version_id, mvw_metric_id, mvw_metric_revision, mvw_metric_caption, mvw_metric_group_id, `mvw_caption`, mvw_partial_coverage, mvw_level,
			mvw_work_id, mvw_work_caption, mvw_work_authors, mvw_work_institution, mvw_work_type, mvw_work_is_private, mvw_work_is_indexed, mvw_work_access_link, `mvw_variable_captions`, `mvw_variable_value_captions`) ";

		$sql .= "SELECT mvr_id, mvr_metric_id, mtr_revision, mtr_caption, mtr_metric_group_id, mvr_caption,
						GROUP_CONCAT(DISTINCT IFNULL(mvl_partial_coverage, geo_partial_coverage) ORDER BY geo_id SEPARATOR ','),
						GROUP_CONCAT(geo_caption ORDER BY geo_id SEPARATOR ','),
						wrk_id, met_title, met_authors, ins_caption,
						wrk_type, wrk_is_private,
						wrk_is_indexed, wrk_access_link, ";
						// Hace un subselect con los nombres de variables
		$sql .= "(SELECT GROUP_CONCAT(mvv_caption ORDER BY mvv_order SEPARATOR '\n')
							FROM variable WHERE mvv_metric_version_level_id = mvl_id),";
						// Hace un subselect distinct con los valores de variables
		$sql .= "(SELECT GROUP_CONCAT(SUB.V1 ORDER BY mvv_order SEPARATOR '\n')
							FROM
									(SELECT mvr_id, mvv_id, mvv_order, GROUP_CONCAT(DISTINCT vvl_caption ORDER BY vvl_variable_id, vvl_order SEPARATOR '\r') AS V1
									FROM metric_version
									JOIN metric_version_level ON mvl_metric_version_id = mvr_id
									JOIN variable ON mvv_metric_version_level_id = mvl_id
									JOIN variable_value_label ON vvl_variable_id = mvv_id
									WHERE mvr_metric_id = ?
									GROUP BY mvr_id, mvv_id) AS SUB
							 WHERE SUB.mvr_id = metric_version.mvr_id)
						FROM metric_version
						JOIN metric ON mvr_metric_id = mtr_id
						JOIN metric_version_level ON mvl_metric_version_id = mvr_id
						JOIN dataset ON mvl_dataset_id = dat_id
						JOIN geography ON dat_geography_id = geo_id
						JOIN work ON dat_work_id = wrk_id
						JOIN metadata ON wrk_metadata_id = met_id
						LEFT JOIN institution ON met_institution_id = ins_id
						WHERE mvr_metric_id = ?
						GROUP BY mvr_id, mvr_metric_id, mvr_caption, wrk_id, met_title";

		App::Db()->exec($sql, array($metricIdShardified, $metricIdShardified));
		Profiling::EndTimer();
	}

	public function ClearMetric($metricId)
	{
		$metricIdShardified = PublishDataTables::Shardified($metricId);

	 	Profiling::BeginTimer();

		$sql = "DELETE FROM snapshot_metric_versions WHERE mvw_metric_id = ?";

		App::Db()->exec($sql, array($metricIdShardified));

		Profiling::EndTimer();
	}

	public function IncrementMetricRevision($metricId)
	{
		$metricIdShardified = PublishDataTables::Shardified($metricId);

	 	Profiling::BeginTimer();

		 $sql = "UPDATE metric SET mtr_revision = mtr_revision + 1 WHERE mtr_id = ?";

		App::Db()->exec($sql, array($metricIdShardified));

		Profiling::EndTimer();
	}

	public function ClearByWork($workId)
	{
		$workIdShardified = PublishDataTables::Shardified($workId);

	 	Profiling::BeginTimer();

		$sql = "DELETE FROM snapshot_metric_versions WHERE mvw_work_id = ?";

		App::Db()->exec($sql, array($workIdShardified));

		Profiling::EndTimer();
	}


}
