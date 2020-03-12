<template>
	<div>
		<div class="btn-group pull-right" style="clear:both">
			<h4 class="title">
				<button title="Quitar" type="button" class="close buttonMargin" v-on:click="clickQuitar">
					<close-icon title="Quitar" />
				</button>

				<button title="Opciones" type="button" class="close "
								v-on:click="clickCustomize" style="margin-right: 3px; margin-left: -4px; font-size: 15px">
					<dots-vertical-icon title="Personalizar" />
				</button>

				<button type="button" v-on:click="toogleRankings" v-if="metric.useRankings()" onmouseup="this.blur()"
								class="close lightButton" :class="(metric.ShowRanking ? 'activeButton' : '')" :title="(metric.ShowRanking ? 'Ocultar ranking' : 'Mostrar ranking')">
					<i class="fa fa-signal" style="margin-left: -4px;" />
				</button>
				<span v-else style="width: 2px; height: 1px; float:right">&nbsp;</span>

				<button v-if="metric.SelectedLevel().Extents" ref="zoomExtentsBtn" type="button"
								class="close lightButton" title="Zoom al indicador" v-on:click="zoomExtents()">
					<i class="fas fa-expand-arrows-alt" style="margin-left: 2px;" />
				</button>

				<span class="dropdown span-margin">
					<button type="button" class="close lightButton" data-toggle="dropdown" title="Comunidades Rurales">
						<i class="fas fa-tree" v-text="getRuralityTextActive()"/>
					</button>
					<ul class="dropdown-menu">
						<li>
							<button type="button" class="close lightButton btn-full" v-on:click="changeRurality('N')">Todo</button>
						</li>
						<li>
							<button type="button" class="close lightButton btn-full" v-on:click="changeRurality('U')">Urbano</button>
						</li>
						<li>
							<button type="button" class="close lightButton btn-full" v-on:click="changeRurality('D')">Urbano disperso</button>
						</li>
						<li>
							<button type="button" class="close lightButton btn-full" v-on:click="changeRurality('R')">Rural</button>
						</li>
						<li>
							<button type="button" class="close lightButton btn-full" v-on:click="changeRurality('L')">Rural disperso</button>
						</li>
					</ul>
				</span>

			</h4>
		</div>
	</div>
</template>

<script>
import CloseIcon from 'vue-material-design-icons/Close.vue';
import DotsVerticalIcon from 'vue-material-design-icons/DotsVertical.vue';
import Mercator from '@/public/js/Mercator';

// https://materialdesignicons.com/cdn/1.9.32/

export default {
	name: 'metricTopButtons',
	props: [
		'metric',
		'clipping',
	],
	components: {
		DotsVerticalIcon,
    CloseIcon
	},
	data() {
		return {
			work: {},
			rurality: '',
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
				extents = m.rectanglesIntersection(extents, this.clipping.Region.Envelope);
			}
			window.SegMap.MapsApi.FitEnvelope(extents);
			this.$refs.zoomExtentsBtn.blur();
		},
		getRuralityTextActive() {
			if(this.rurality === 'N') {
				return '';
			}else if(this.rurality === 'U') {
				return ' - Urbano';
			}else if(this.rurality === 'D') {
				return ' - Urbano disperso';
			}else if(this.rurality === 'R') {
				return ' - Rural';
			}else if(this.rurality === 'L') {
				 return ' - Rural disperso';
			}
		},
		changeRurality(mode) {
			this.metric.properties.SelectedUrbanity = mode;
			window.SegMap.SaveRoute.UpdateRoute();
			window.SegMap.UpdateMap();
			this.rurality = mode;
		},
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

.buttonMargin {
  margin-right: -2px;
  margin-top: -4px;
	margin-left: -3px;
}

.lightButton {
	font-size: 12px;
  padding: 4px;
	line-height: 1em;
}

.activeButton {
	opacity: .45;
}
.btn-full {
	width: 100%;
}
.span-margin {
	margin-right: 40px;
}
</style>
