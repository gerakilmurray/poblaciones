import err from '@/common/js/err';
import axios from 'axios';

export default Search;

function Search(view, revision, searchType) {
	this.view = view;
	this.searchType = searchType;
	this.revision = revision;
};

Search.prototype.StartSearch = function (t) {
	if (this.preSearch(t)) {
		return;
	}
	if(this.view.retCancel !== null)
	{
		this.view.retCancel('cancelled');
		this.view.retCancel = null;
	}
	var CancelToken = axios.CancelToken;
	var retCancel = null;
	var view = this.view;
	var loc = this;
	view.loading = true;
	axios.get(/*window.host + */ '/services/search', {
    params: { q: t, f: this.searchType, w: this.revision },
		cancelToken: new CancelToken(function executor(c) { retCancel = c; })
		})
		.then(function(res) {
			loc.LoadResults(res.data, t);
			view.loading = false;
		}).catch(function(error) {
			view.loading = false;
			err.errDialog('search', 'completar la búsqueda solicitada', error);
		});
	view.retCancel = retCancel;
};

Search.prototype.preSearch = function (text) {
	var coordParser = require('./ParseCoordinate');
	var ret = new coordParser(text);
	if (!ret.success) {
		return false;
	}
	var item = {
		id: null,
		caption: ret.display,
		type: "P",
		extraIds: "",
		symbol: "fas fa-map-marker-alt",
		Lat: ret.result.y,
		Lon: ret.result.x,
		extra: "Ubicación"
	};
	this.LoadResults([item], text);
	return true;
};

Search.prototype.LoadResults = function(list, t) {
	var view = this.view;
	view.searched = t;
	view.autolist = list.map(function (el) {
		el.highlighted = el.caption;
		el.class = '';
		return el;
	});
	if (view.autolist.length === 0) {
		view.autolist = [{
			type: 'N',
			highlighted: 'No se encontraron resultados.',
		}];
	}
};
