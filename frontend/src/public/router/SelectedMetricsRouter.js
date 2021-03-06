import h from '@/public/js/helper';
import ActiveSelectedMetric from '@/public/classes/ActiveSelectedMetric';
import err from '@/common/js/err';

export default SelectedMetricsRouter;

function SelectedMetricsRouter() {
};

SelectedMetricsRouter.prototype.GetSettings = function() {
	return {
		blockSignature: 'l=',
		startChar: null,
		endChar: null,
		groupSeparator: ';',
		itemSeparator: '!',
		useKeyValue: true
	};
};

SelectedMetricsRouter.prototype.ToRoute = function (askeyarray) {
	var segmentedMap = window.SegMap;
	var ret = [];
	for (var n = 0; n < segmentedMap.Metrics.metrics.length; n++) {
		ret.push(this.SelectedMetricToRoute(segmentedMap.Metrics.metrics[n], askeyarray));
	}
	return ret;
};

SelectedMetricsRouter.prototype.SelectedMetricToRoute = function (activeSelectedMetric, askeyarray) {
	if (activeSelectedMetric.properties === null) {
		throw new Error('No properties has been set.');
	}
	var ret = [];
	ret.push([activeSelectedMetric.properties.Metric.Id]);
	ret.push(['v', activeSelectedMetric.properties.SelectedVersionIndex, -1]);
	ret.push(['a', activeSelectedMetric.SelectedVersion().SelectedLevelIndex, 0]);
	ret.push(['i', activeSelectedMetric.SelectedLevel().SelectedVariableIndex, 0]);
	ret.push(['k', this.GetRanking(activeSelectedMetric), '']);
	ret.push(['c', this.Boolean(activeSelectedMetric.SelectedVersion().LabelsCollapsed), '0']);

	ret.push(['m', activeSelectedMetric.properties.SummaryMetric, 'N']);
	ret.push(['u', activeSelectedMetric.properties.SelectedUrbanity, 'N']);
	if (activeSelectedMetric.SelectedLevel().Pinned) {
		ret.push(['l', '1']);
	}
	ret.push(['t', activeSelectedMetric.properties.Transparency, 'M']);

	if (activeSelectedMetric.SelectedVariable()) {
		if (activeSelectedMetric.SelectedVariable().CustomPattern !== activeSelectedMetric.SelectedVariable().Pattern) {
			ret.push(['p', activeSelectedMetric.SelectedVariable().CustomPattern, '']);
		}
		ret.push(['d', this.Boolean(activeSelectedMetric.SelectedVariable().ShowDescriptions), '0']);
		ret.push(['s', this.Boolean(activeSelectedMetric.SelectedVariable().ShowValues), '0']);
	}
	// bloque de estado de variables. las variables van separadas por @, e indican visible y luego lista de visible de valores.
	var variablesInfo = this.VariablesToRoute(activeSelectedMetric);
	ret.push(['w', variablesInfo, '']);

	//$levelId = Params::Get('a');
	//$excludedValues = Params::Get('x');
	//$collapsed = Params::Get('c');
	//$metric = Params::Get('m');
	//$variableId = Params::Get('i');
	//$urbanity = Params::Get('u');
	if (askeyarray) {
		ret = this.transformArrayListToKeyList(ret);
	}
	return ret;
};

SelectedMetricsRouter.prototype.transformArrayListToKeyList = function (list) {
	var ret = {};
	for (var n = 0; n < list.length; n++) {
		if (list[n].length === 1) {
			ret[''] = list[n][0];
		} else {
				ret[list[n][0]] = list[n][1];
		}
	}
	return ret;
};

SelectedMetricsRouter.prototype.GetRanking = function (metric) {
	if (!metric.ShowRanking) {
		return '';
	}
	var ret = '';
	if (metric.RankingSize != 10) {
		ret += metric.RankingSize;
	}
	ret += metric.RankingDirection;
	return ret;
};

SelectedMetricsRouter.prototype.ParseRanking = function (value) {
	var size = 10;
	var direction = 'D';
	var show = false;
	if (value !== null) {
		show = true;
		if (value.indexOf('A') !== -1) {
			direction = 'A';
		}
		value = value.replace('A', '');
		value = value.replace('D', '');
		if (value.length > 0) {
			size = parseInt(value);
			if (size > 50 || size < 10) {
				size = 10;
			}
		}
	}
	return { Size: size, Direction: direction, Show: show };
};

SelectedMetricsRouter.prototype.Boolean = function (value) {
	return (value && value !== '0' ? '1' : '0');
};

SelectedMetricsRouter.prototype.VariablesToRoute = function (activeSelectedMetric) {
	var ret = '';
	for (var v = 0; v < activeSelectedMetric.SelectedLevel().Variables.length; v++) {
		var variable = activeSelectedMetric.SelectedLevel().Variables[v];
		ret += this.Boolean(variable.Visible);
		var vals = '';
		var allVisible = true;
		for (var vl = 0; vl < variable.ValueLabels.length; vl++) {
			if (variable.ValueLabels[vl].Visible) {
				vals += '1';
			} else {
				vals += '0';
				allVisible = false;
			}
		}
		if (!allVisible) ret += vals;

		if (v < activeSelectedMetric.SelectedLevel().Variables.length - 1) {
			ret += ',';
		}
	}
	return ret;
};

SelectedMetricsRouter.prototype.FromRoute = function (args, updateRoute, skipRestore) {
	var metrics =	this.parseMetrics(args);
	this.LoadMetrics(metrics, updateRoute, skipRestore);
};

SelectedMetricsRouter.prototype.LoadMetrics = function (metrics, updateRoute, skipRestore) {
	// Se fija si cambian las métricas
	var segmentedMap = window.SegMap;
	if (metrics.length === 0) {
		segmentedMap.Metrics.ClearUserMetrics();
		return;
	}
	var currentMetrics = this.parseMetrics(this.ToRoute(true));
	// Si cambiaron, recarga todos
	// Una vez cargadas (o si no cambiaron) les setea los estados
	if (this.metricsChanged(metrics, currentMetrics)) {
		var loc = this;
		var metricIds = '';
		for (var l = 0; l < metrics.length; l++) {
			metricIds += metrics[l].Id + (l < metrics.length - 1 ? ',' : '');
		}
		window.SegMap.Get(window.host + '/services/metrics/GetSelectedMetrics', {
			params: { l: metricIds },
		}).then(function (res) {
			segmentedMap.SaveRoute.Disabled = true;
			segmentedMap.Metrics.ClearUserMetrics();

			for (var n = 0; n < metrics.length; n++) {
				var selectedMetric = res.data[n];
				if (selectedMetric != null) {
					var activeMetric = new ActiveSelectedMetric(selectedMetric, false);
					if (!skipRestore) {
						loc.RestoreMetricState(activeMetric, metrics[n]);
					}
					activeMetric.properties.SelectedVersionIndex = parseInt(activeMetric.properties.SelectedVersionIndex);
					activeMetric.UpdateLevel();
					segmentedMap.Metrics.AppendStandardMetric(activeMetric);
				}
			}
			segmentedMap.Labels.UpdateMap();
			segmentedMap.SaveRoute.Disabled = false;
			if (updateRoute) {
				segmentedMap.SaveRoute.UpdateRoute();
			}
		}).catch(function (error) {
			err.errDialog('GetSelectedMetrics', 'obtener la información para los indicadores seleccionados', error);
		});
	} else {
		this.restoreMetricStates(metrics);
	}
};

SelectedMetricsRouter.prototype.metricsChanged = function (metrics, currentMetrics) {
	if (metrics.length !== currentMetrics.length) {
		return true;
	}
	for (var l = 0; l < metrics.length; l++) {
		if (metrics[l].Id !== currentMetrics[l].Id) {
			return true;
		}
	}
	return false;
};

SelectedMetricsRouter.prototype.parseMetrics = function (args) {
	var metrics = [];
	for (var metricKey in args) {
		var metric = args[metricKey];
		metrics.push(this.parseMetric(metric));
	}
	return metrics;
};

SelectedMetricsRouter.prototype.parseMetric = function (values) {
	var id = h.getSafeValue(values, '');
	var versionIndex = h.getSafeValue(values, 'v', -1);
	var levelIndex = h.getSafeValue(values, 'a', 0);
	var variableIndex = h.getSafeValue(values, 'i', 0);
	var labelsCollapsed = h.getSafeValue(values, 'c', false);
	var summaryMetric = h.getSafeValue(values, 'm', 'N');
	var urbanity = h.getSafeValue(values, 'u', 'N');
	var pinnedLevel = h.getSafeValue(values, 'l', '');
	var showDescriptions = h.getSafeValue(values, 'd', '0');
	var showValues = h.getSafeValue(values, 's', '0');
	var ranking = h.getSafeValue(values, 'k', null);
	var customPattern = h.getSafeValue(values, 'p', '');
	var transparency = h.getSafeValue(values, 't', 'M');
	var variableStates = h.getSafeValue(values, 'w', null);

	return {
		Id: parseInt(id),
		VersionIndex: versionIndex,
		LevelIndex: levelIndex,
		VariableIndex: variableIndex,
		LabelsCollapsed: labelsCollapsed,
		SummaryMetric: summaryMetric,
		Urbanity: urbanity,
		ShowDescriptions: showDescriptions,
		ShowValues: showValues,
		ShowRanking: this.ParseRanking(ranking)['Show'],
		RankingSize: this.ParseRanking(ranking)['Size'],
		RankingDirection: this.ParseRanking(ranking)['Direction'],
		Transparency: transparency,
		PinnedLevel: pinnedLevel,
		CustomPattern: (customPattern === '' ? '' : parseInt(customPattern)),
		VariableStates: (variableStates ? variableStates.split(',') : [])
	};
};


SelectedMetricsRouter.prototype.restoreMetricStates = function (states) {
	var segmentedMap = window.SegMap;
	for (var n = 0; n < segmentedMap.Metrics.metrics.length; n++) {
		var activeMetric = segmentedMap.Metrics.metrics[n];
		var state = states[n];
		if (this.RestoreMetricState(activeMetric, state)) {
			activeMetric.UpdateMap();
		}
	}
};

SelectedMetricsRouter.prototype.RestoreMetricState = function (activeSelectedMetric, state) {
	var mapChanged = false;
	var selectedMetric = activeSelectedMetric.properties;
	var versionIndex = parseInt(state.VersionIndex);
	if (versionIndex !== -1 && versionIndex !== selectedMetric.SelectedVersionIndex &&
		versionIndex < selectedMetric.Versions.length) {
		selectedMetric.SelectedVersionIndex = versionIndex;
		mapChanged = true;
	}
	var version = selectedMetric.Versions[selectedMetric.SelectedVersionIndex];
	var levelIndex = parseInt(state.LevelIndex);
	if (levelIndex !== version.SelectedLevelIndex &&
		levelIndex < version.Levels.length) {
		version.SelectedLevelIndex = levelIndex;
		if (state.PinnedLevel === '1') {
			activeSelectedMetric.SelectedLevel().Pinned = true;
		}
		mapChanged = true;
	}
	var level = version.Levels[version.SelectedLevelIndex];
	var variableIndex = parseInt(state.VariableIndex);
	if (variableIndex !== level.SelectedVariableIndex &&
		variableIndex < level.Variables.length) {
		level.SelectedVariableIndex = variableIndex;
		mapChanged = true;
	}
	if (state.LabelsCollapsed !== version.LabelsCollapsed) {
		version.LabelsCollapsed = state.LabelsCollapsed;
	}

	if (state.ShowRanking !== activeSelectedMetric.ShowRanking) {
		activeSelectedMetric.ShowRanking = state.ShowRanking;
	}
	if (state.RankingDirection !== activeSelectedMetric.RankingDirection) {
		activeSelectedMetric.RankingDirection = state.RankingDirection;
	}
	if (state.RankingSize !== activeSelectedMetric.RankingSize) {
		activeSelectedMetric.RankingSize = state.RankingSize;
	}

	if (state.LabelsCollapsed !== version.LabelsCollapsed) {
		version.LabelsCollapsed = state.LabelsCollapsed;
	}
	if (selectedMetric.SummaryMetric !== state.SummaryMetric) {
		selectedMetric.SummaryMetric = state.SummaryMetric;
	}
	if (selectedMetric.SelectedUrbanity !== state.Urbanity) {
		selectedMetric.SelectedUrbanity = state.Urbanity;
		mapChanged = true;
	}
	if (activeSelectedMetric.SelectedVariable()) {
		if (activeSelectedMetric.SelectedVariable().ShowDescriptions !== state.ShowDescriptions) {
			activeSelectedMetric.SelectedVariable().ShowDescriptions = state.ShowDescriptions;
			mapChanged = true;
		}
		if (activeSelectedMetric.SelectedVariable().CustomPattern !== state.CustomPattern) {
			activeSelectedMetric.SelectedVariable().CustomPattern = state.CustomPattern;
			mapChanged = true;
		}
		if (activeSelectedMetric.SelectedVariable().ShowValues !== state.ShowValues) {
			activeSelectedMetric.SelectedVariable().ShowValues = state.ShowValues;
			mapChanged = true;
		}
	}
	if (selectedMetric.Transparency !== state.Transparency) {
		selectedMetric.Transparency = state.Transparency;
		mapChanged = true;
	}
	if (state.VariableStates.length === level.Variables.length) {
		for (var v = 0; v < level.Variables.length; v++) {
			var variable = level.Variables[v];
			var st = state.VariableStates[v];
			var value = (st.substring(0, 1) === '1');
			if (variable.Visible !== value) {
				variable.Visible = value;
				mapChanged = true;
			}
			for (var lb = 0; lb < variable.ValueLabels.length; lb++) {
				var val = true;
				if (lb + 1 < st.length) {
					val = (st.substr(lb + 1, 1) === '1');
				}
				if (variable.ValueLabels[lb].Visible !== val) {
					variable.ValueLabels[lb].Visible = val;
					mapChanged = true;
				}
			}
		}
	}
	return mapChanged;
};
