<template>
	<div>
		<div class="btn-group pull-right" style="clear:both">
			<h4 class="title">
				<mp-close-button v-on:click="clickQuitar" />

				<button title="Opciones" type="button" class="close "
								v-on:click="clickCustomize" style="margin-right: 7px; margin-left: -2px; margin-top: 4px;
font-size: 12px">
					<i class="fas fa-sliders-h"></i>
				</button>

				<button type="button" v-on:click="toogleRankings" v-if="metric.useRankings()" onmouseup="this.blur()"
								class="close lightButton" :class="(metric.ShowRanking ? 'activeButton' : '')" :title="(metric.ShowRanking ? 'Ocultar ranking' : 'Mostrar ranking')">
					<i class="fa fa-signal" style="margin-left: -6px;" />
				</button>
				<span v-else style="width: 2px; height: 1px; float:right">&nbsp;</span>

				<button v-if="metric.SelectedLevel().Extents" ref="zoomExtentsBtn" type="button"
								class="close lightButton" title="Zoom al indicador" v-on:click="zoomExtents()">
					<i class="fas fa-expand-arrows-alt" style="margin-left: 2px;" />
				</button>

			</h4>
		</div>
	</div>
</template>

<script>
import Mercator from '@/public/js/Mercator';

// https://materialdesignicons.com/cdn/1.9.32/

export default {
	name: 'metricTopButtons',
	props: [
		'metric',
		'clipping',
	],
	components: {

	},
	data() {
		return {
			work: {},
		};
	},
	methods: {
		addMetric(id) {
			window.SegMap.AddMetricById(id);
		},
		clickCustomize(e) {
			e.preventDefault();
			window.Popups.MetricCustomize.show(this.metric);
		},
		clickQuitar(e) {
			e.preventDefault();
			this.metric.Remove();
		},
		toogleRankings() {
			this.metric.ShowRanking = !this.metric.ShowRanking;
			window.SegMap.SaveRoute.UpdateRoute();
			if (this.metric.ShowRanking) {
				this.$emit('RankingShown');
			}
		},
		zoomExtents() {
			var extents = this.metric.SelectedLevel().Extents;
			if (!window.SegMap.Clipping.FrameHasNoClipping()) {
				var m = new Mercator();
				if (window.SegMap.Clipping.FrameHasClippingCircle()) {
					var intersect = m.rectanglesIntersection(extents, this.clipping.Region.Envelope);
					if (this.shouldClearSelection(intersect, extents)) {
						window.SegMap.Clipping.ResetClippingCircle();
					}
				}
				if (window.SegMap.Clipping.FrameHasClippingCircle() == false &&
								window.SegMap.Clipping.FrameHasClippingRegionId()) {
					var intersect = m.rectanglesIntersection(extents, this.clipping.Region.Envelope);
					if (this.shouldClearSelection(intersect, extents)) {
						window.SegMap.Clipping.ResetClippingRegion();
					}
				}
			}
			window.SegMap.MapsApi.FitEnvelope(extents, true);
			this.$refs.zoomExtentsBtn.blur();
		},
		shouldClearSelection(intersect, extents) {
			if (intersect === null) {
				return true;
			}
			var m = new Mercator();
			// Se fija si el área de intersección es menor al área del indicador
			// con 10% de tolerancia
			var area1 = m.rectanglePixelArea(intersect);
			var area2 = m.rectanglePixelArea(extents);
			return area1 < area2 * .9;
		}
	},
	computed: {

	}
};
</script>

<style scoped>
  .vellipsis:after {
  content: '\2807';
  font-size: .8em;
  }

.lightButton {
	font-size: 12px;
  padding: 4px;
	line-height: 1em;
}

.activeButton {
	opacity: .45;
}
</style>
