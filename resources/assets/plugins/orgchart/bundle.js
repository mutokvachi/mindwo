/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';
	
	var _orgchartMin = __webpack_require__(1);
	
	var _orgchartMin2 = _interopRequireDefault(_orgchartMin);
	
	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
	
	document.addEventListener('DOMContentLoaded', function () {
	
	  var datascource = {"id":212,"name":"Demo212 Last212","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":10,"href":"http://mindwo.demo/employee/profile/212","relationship":"101","hasParent":true,"top":true,"parentUrl":"http://mindwo.demo/organization/chart/0","children":[{"id":154,"name":"Demo154 Last154","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":6,"href":"http://mindwo.demo/employee/profile/154","relationship":"111","hasParent":true,"top":false,"children":[{"id":105,"name":"Demo105 Last105","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":23,"href":"http://mindwo.demo/employee/profile/105","relationship":"111","hasParent":true,"top":false,"children":[{"id":108,"name":"Demo108 Last108","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/108","relationship":"110","hasParent":true,"top":false},{"id":112,"name":"Demo112 Last112","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/112","relationship":"110","hasParent":true,"top":false},{"id":114,"name":"Demo114 Last114","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":2,"href":"http://mindwo.demo/employee/profile/114","relationship":"111","hasParent":true,"top":false,"children":[{"id":111,"name":"Demo111 Last111","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/111","relationship":"110","hasParent":true,"top":false},{"id":59,"name":"Demo59 Last59","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/59","relationship":"110","hasParent":true,"top":false}]},{"id":115,"name":"Demo115 Last115","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/115","relationship":"110","hasParent":true,"top":false},{"id":117,"name":"Demo117 Last117","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/117","relationship":"110","hasParent":true,"top":false},{"id":120,"name":"Demo120 Last120","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/120","relationship":"110","hasParent":true,"top":false},{"id":133,"name":"Demo133 Last133","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/133","relationship":"110","hasParent":true,"top":false},{"id":134,"name":"Demo134 Last134","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/134","relationship":"110","hasParent":true,"top":false},{"id":135,"name":"Demo135 Last135","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/135","relationship":"110","hasParent":true,"top":false},{"id":136,"name":"Demo136 Last136","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/136","relationship":"110","hasParent":true,"top":false},{"id":137,"name":"Demo137 Last137","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/137","relationship":"110","hasParent":true,"top":false},{"id":138,"name":"Demo138 Last138","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/138","relationship":"110","hasParent":true,"top":false},{"id":139,"name":"Demo139 Last139","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/139","relationship":"110","hasParent":true,"top":false},{"id":140,"name":"Demo140 Last140","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/140","relationship":"110","hasParent":true,"top":false},{"id":141,"name":"Demo141 Last141","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/141","relationship":"110","hasParent":true,"top":false},{"id":142,"name":"Demo142 Last142","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/142","relationship":"110","hasParent":true,"top":false},{"id":144,"name":"Demo144 Last144","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/144","relationship":"110","hasParent":true,"top":false},{"id":145,"name":"Demo145 Last145","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/145","relationship":"110","hasParent":true,"top":false},{"id":46,"name":"Demo46 Last46","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/46","relationship":"110","hasParent":true,"top":false},{"id":57,"name":"Demo57 Last57","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/57","relationship":"110","hasParent":true,"top":false},{"id":60,"name":"Demo60 Last60","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/60","relationship":"110","hasParent":true,"top":false},{"id":78,"name":"Demo78 Last78","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/78","relationship":"110","hasParent":true,"top":false},{"id":88,"name":"Demo88 Last88","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/88","relationship":"110","hasParent":true,"top":false}]},{"id":148,"name":"Demo148 Last148","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/148","relationship":"110","hasParent":true,"top":false},{"id":152,"name":"Demo152 Last152","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/152","relationship":"110","hasParent":true,"top":false},{"id":153,"name":"Demo153 Last153","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/153","relationship":"110","hasParent":true,"top":false},{"id":164,"name":"Demo164 Last164","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/164","relationship":"110","hasParent":true,"top":false},{"id":37,"name":"Demo37 Last37","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":46,"href":"http://mindwo.demo/employee/profile/37","relationship":"111","hasParent":true,"top":false,"children":[{"id":100,"name":"Demo100 Last100","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/100","relationship":"110","hasParent":true,"top":false},{"id":101,"name":"Demo101 Last101","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/101","relationship":"110","hasParent":true,"top":false},{"id":45,"name":"Demo45 Last45","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/45","relationship":"110","hasParent":true,"top":false},{"id":47,"name":"Demo47 Last47","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/47","relationship":"110","hasParent":true,"top":false},{"id":52,"name":"Demo52 Last52","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/52","relationship":"110","hasParent":true,"top":false},{"id":53,"name":"Demo53 Last53","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/53","relationship":"110","hasParent":true,"top":false},{"id":54,"name":"Demo54 Last54","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/54","relationship":"110","hasParent":true,"top":false},{"id":55,"name":"Demo55 Last55","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/55","relationship":"110","hasParent":true,"top":false},{"id":56,"name":"Demo56 Last56","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/56","relationship":"110","hasParent":true,"top":false},{"id":58,"name":"Demo58 Last58","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/58","relationship":"110","hasParent":true,"top":false},{"id":61,"name":"Demo61 Last61","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/61","relationship":"110","hasParent":true,"top":false},{"id":62,"name":"Demo62 Last62","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/62","relationship":"110","hasParent":true,"top":false},{"id":63,"name":"Demo63 Last63","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/63","relationship":"110","hasParent":true,"top":false},{"id":64,"name":"Demo64 Last64","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/64","relationship":"110","hasParent":true,"top":false},{"id":65,"name":"Demo65 Last65","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/65","relationship":"110","hasParent":true,"top":false},{"id":66,"name":"Demo66 Last66","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/66","relationship":"110","hasParent":true,"top":false},{"id":67,"name":"Demo67 Last67","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/67","relationship":"110","hasParent":true,"top":false},{"id":68,"name":"Demo68 Last68","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/68","relationship":"110","hasParent":true,"top":false},{"id":69,"name":"Demo69 Last69","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/69","relationship":"110","hasParent":true,"top":false},{"id":70,"name":"Demo70 Last70","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/70","relationship":"110","hasParent":true,"top":false},{"id":71,"name":"Demo71 Last71","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/71","relationship":"110","hasParent":true,"top":false},{"id":72,"name":"Demo72 Last72","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/72","relationship":"110","hasParent":true,"top":false},{"id":73,"name":"Demo73 Last73","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/73","relationship":"110","hasParent":true,"top":false},{"id":74,"name":"Demo74 Last74","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/74","relationship":"110","hasParent":true,"top":false},{"id":76,"name":"Demo76 Last76","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/76","relationship":"110","hasParent":true,"top":false},{"id":77,"name":"Demo77 Last77","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/77","relationship":"110","hasParent":true,"top":false},{"id":79,"name":"Demo79 Last79","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/79","relationship":"110","hasParent":true,"top":false},{"id":80,"name":"Demo80 Last80","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/80","relationship":"110","hasParent":true,"top":false},{"id":81,"name":"Demo81 Last81","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/81","relationship":"110","hasParent":true,"top":false},{"id":82,"name":"Demo82 Last82","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/82","relationship":"110","hasParent":true,"top":false},{"id":83,"name":"Demo83 Last83","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/83","relationship":"110","hasParent":true,"top":false},{"id":84,"name":"Demo84 Last84","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/84","relationship":"110","hasParent":true,"top":false},{"id":85,"name":"Demo85 Last85","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/85","relationship":"110","hasParent":true,"top":false},{"id":86,"name":"Demo86 Last86","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/86","relationship":"110","hasParent":true,"top":false},{"id":87,"name":"Demo87 Last87","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/87","relationship":"110","hasParent":true,"top":false},{"id":89,"name":"Demo89 Last89","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/89","relationship":"110","hasParent":true,"top":false},{"id":90,"name":"Demo90 Last90","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/90","relationship":"110","hasParent":true,"top":false},{"id":91,"name":"Demo91 Last91","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/91","relationship":"110","hasParent":true,"top":false},{"id":92,"name":"Demo92 Last92","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/92","relationship":"110","hasParent":true,"top":false},{"id":93,"name":"Demo93 Last93","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/93","relationship":"110","hasParent":true,"top":false},{"id":94,"name":"Demo94 Last94","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/94","relationship":"110","hasParent":true,"top":false},{"id":95,"name":"Demo95 Last95","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/95","relationship":"110","hasParent":true,"top":false},{"id":96,"name":"Demo96 Last96","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/96","relationship":"110","hasParent":true,"top":false},{"id":97,"name":"Demo97 Last97","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/97","relationship":"110","hasParent":true,"top":false},{"id":98,"name":"Demo98 Last98","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/98","relationship":"110","hasParent":true,"top":false},{"id":99,"name":"Demo99 Last99","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/99","relationship":"110","hasParent":true,"top":false}]}]},{"id":155,"name":"Demo155 Last155","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":8,"href":"http://mindwo.demo/employee/profile/155","relationship":"111","hasParent":true,"top":false,"children":[{"id":147,"name":"Demo147 Last147","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/147","relationship":"110","hasParent":true,"top":false},{"id":157,"name":"Demo157 Last157","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/157","relationship":"110","hasParent":true,"top":false},{"id":158,"name":"Demo158 Last158","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/158","relationship":"110","hasParent":true,"top":false},{"id":159,"name":"Demo159 Last159","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/159","relationship":"110","hasParent":true,"top":false},{"id":160,"name":"Demo160 Last160","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/160","relationship":"110","hasParent":true,"top":false},{"id":161,"name":"Demo161 Last161","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/161","relationship":"110","hasParent":true,"top":false},{"id":162,"name":"Demo162 Last162","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/162","relationship":"110","hasParent":true,"top":false},{"id":229,"name":"Demo229 Last229","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/229","relationship":"110","hasParent":true,"top":false}]},{"id":188,"name":"Demo188 Last188","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":1,"href":"http://mindwo.demo/employee/profile/188","relationship":"111","hasParent":true,"top":false,"children":[{"id":189,"name":"Demo189 Last189","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/189","relationship":"100","hasParent":true,"top":false}]},{"id":190,"name":"Demo190 Last190","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":8,"href":"http://mindwo.demo/employee/profile/190","relationship":"111","hasParent":true,"top":false,"children":[{"id":191,"name":"Demo191 Last191","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/191","relationship":"110","hasParent":true,"top":false},{"id":192,"name":"Demo192 Last192","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/192","relationship":"110","hasParent":true,"top":false},{"id":193,"name":"Demo193 Last193","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/193","relationship":"110","hasParent":true,"top":false},{"id":194,"name":"Demo194 Last194","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":1,"href":"http://mindwo.demo/employee/profile/194","relationship":"111","hasParent":true,"top":false,"children":[{"id":195,"name":"Demo195 Last195","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/195","relationship":"100","hasParent":true,"top":false}]},{"id":196,"name":"Demo196 Last196","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/196","relationship":"110","hasParent":true,"top":false},{"id":197,"name":"Demo197 Last197","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/197","relationship":"110","hasParent":true,"top":false},{"id":198,"name":"Demo198 Last198","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/198","relationship":"110","hasParent":true,"top":false},{"id":199,"name":"Demo199 Last199","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":10,"href":"http://mindwo.demo/employee/profile/199","relationship":"111","hasParent":true,"top":false,"children":[{"id":200,"name":"Demo200 Last200","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/200","relationship":"110","hasParent":true,"top":false},{"id":201,"name":"Demo201 Last201","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/201","relationship":"110","hasParent":true,"top":false},{"id":202,"name":"Demo202 Last202","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/202","relationship":"110","hasParent":true,"top":false},{"id":203,"name":"Demo203 Last203","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/203","relationship":"110","hasParent":true,"top":false},{"id":204,"name":"Demo204 Last204","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/204","relationship":"110","hasParent":true,"top":false},{"id":205,"name":"Demo205 Last205","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/205","relationship":"110","hasParent":true,"top":false},{"id":206,"name":"Demo206 Last206","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/206","relationship":"110","hasParent":true,"top":false},{"id":207,"name":"Demo207 Last207","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/207","relationship":"110","hasParent":true,"top":false},{"id":208,"name":"Demo208 Last208","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/208","relationship":"110","hasParent":true,"top":false},{"id":209,"name":"Demo209 Last209","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/209","relationship":"110","hasParent":true,"top":false}]}]},{"id":219,"name":"Demo219 Last219","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/219","relationship":"110","hasParent":true,"top":false},{"id":220,"name":"Demo220 Last220","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":2,"href":"http://mindwo.demo/employee/profile/220","relationship":"111","hasParent":true,"top":false,"children":[{"id":222,"name":"Demo222 Last222","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":1,"href":"http://mindwo.demo/employee/profile/222","relationship":"111","hasParent":true,"top":false,"children":[{"id":221,"name":"Demo221 Last221","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":1,"href":"http://mindwo.demo/employee/profile/221","relationship":"101","hasParent":true,"top":false,"children":[{"id":223,"name":"Demo223 Last223","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/223","relationship":"100","hasParent":true,"top":false}]}]},{"id":224,"name":"Demo224 Last224","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/224","relationship":"110","hasParent":true,"top":false}]},{"id":225,"name":"Demo225 Last225","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/225","relationship":"110","hasParent":true,"top":false},{"id":226,"name":"Demo226 Last226","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/226","relationship":"110","hasParent":true,"top":false},{"id":227,"name":"Demo227 Last227","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":3,"href":"http://mindwo.demo/employee/profile/227","relationship":"111","hasParent":true,"top":false,"children":[{"id":165,"name":"Demo165 Last165","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":5,"href":"http://mindwo.demo/employee/profile/165","relationship":"111","hasParent":true,"top":false,"children":[{"id":166,"name":"Demo166 Last166","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/166","relationship":"110","hasParent":true,"top":false},{"id":167,"name":"Demo167 Last167","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/167","relationship":"110","hasParent":true,"top":false},{"id":168,"name":"Demo168 Last168","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/168","relationship":"110","hasParent":true,"top":false},{"id":169,"name":"Demo169 Last169","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/169","relationship":"110","hasParent":true,"top":false},{"id":170,"name":"Demo170 Last170","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/170","relationship":"110","hasParent":true,"top":false}]},{"id":171,"name":"Demo171 Last171","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":1,"href":"http://mindwo.demo/employee/profile/171","relationship":"111","hasParent":true,"top":false,"children":[{"id":172,"name":"Demo172 Last172","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/172","relationship":"100","hasParent":true,"top":false}]},{"id":210,"name":"Demo210 Last210","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/210","relationship":"110","hasParent":true,"top":false}]},{"id":228,"name":"Demo228 Last228","title":"Project manager","avatar":"http://mindwo.demo/assets/global/avatars/default_avatar_big.jpg","subordinates":0,"href":"http://mindwo.demo/employee/profile/228","relationship":"110","hasParent":true,"top":false}]},
	      orgchart = new _orgchartMin2.default({
	    'chartContainer': '#chart-container',
	    'data': datascource,
	    'nodeContent': 'title',
	    'verticalDepth': 3,
	    'depth': 3
	  });
	});

/***/ },
/* 1 */
/***/ function(module, exports) {

	"use strict";
	
	var _typeof2 = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };
	
	function _toConsumableArray(e) {
	  if (Array.isArray(e)) {
	    for (var t = 0, n = Array(e.length); t < e.length; t++) {
	      n[t] = e[t];
	    }return n;
	  }return Array.from(e);
	}function _classCallCheck(e, t) {
	  if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
	}Object.defineProperty(exports, "__esModule", { value: !0 });var _typeof = "function" == typeof Symbol && "symbol" == _typeof2(Symbol.iterator) ? function (e) {
	  return typeof e === "undefined" ? "undefined" : _typeof2(e);
	} : function (e) {
	  return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e === "undefined" ? "undefined" : _typeof2(e);
	},
	    _createClass = function () {
	  function e(e, t) {
	    for (var n = 0; n < t.length; n++) {
	      var i = t[n];i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i);
	    }
	  }return function (t, n, i) {
	    return n && e(t.prototype, n), i && e(t, i), t;
	  };
	}(),
	    OrgChart = function () {
	  function e(t) {
	    _classCallCheck(this, e), this._name = "OrgChart", Promise.prototype.finally = function (e) {
	      var t = this.constructor;return this.then(function (n) {
	        return t.resolve(e()).then(function () {
	          return n;
	        });
	      }, function (n) {
	        return t.resolve(e()).then(function () {
	          throw n;
	        });
	      });
	    };var n = this,
	        i = { nodeTitle: "name", nodeId: "id", toggleSiblingsResp: !1, depth: 999, chartClass: "", exportButton: !1, exportFilename: "OrgChart", parentNodeSymbol: "fa-users", draggable: !1, direction: "t2b", pan: !1, zoom: !1 },
	        r = Object.assign(i, t),
	        s = r.data,
	        a = document.createElement("div"),
	        o = document.querySelector(r.chartContainer);if (this.options = r, delete this.options.data, this.chart = a, this.chartContainer = o, a.dataset.options = JSON.stringify(r), a.setAttribute("class", "orgchart" + ("" !== r.chartClass ? " " + r.chartClass : "") + ("t2b" !== r.direction ? " " + r.direction : "")), "object" === ("undefined" == typeof s ? "undefined" : _typeof(s))) this.buildHierarchy(a, r.ajaxURL ? s : this._attachRel(s, "00"), 0);else if ("string" == typeof s && s.startsWith("#")) this.buildHierarchy(a, this._buildJsonDS(document.querySelector(s).children[0]), 0);else {
	      var l = document.createElement("i");l.setAttribute("class", "fa fa-circle-o-notch fa-spin spinner"), a.appendChild(l), this._getJSON(s).then(function (e) {
	        n.buildHierarchy(a, r.ajaxURL ? e : n._attachRel(e, "00"), 0);
	      }).catch(function (e) {
	        console.error("failed to fetch datasource for orgchart", e);
	      }).finally(function () {
	        var e = a.querySelector(".spinner");e.parentNode.removeChild(e);
	      });
	    }if (a.addEventListener("click", this._clickChart.bind(this)), r.exportButton && !o.querySelector(".oc-export-btn")) {
	      var d = document.createElement("button"),
	          c = document.createElement("a");d.setAttribute("class", "oc-export-btn" + ("" !== r.chartClass ? " " + r.chartClass : "")), d.innerHTML = "Export", d.addEventListener("click", this._clickExportButton.bind(this)), c.setAttribute("class", "oc-download-btn" + ("" !== r.chartClass ? " " + r.chartClass : "")), c.setAttribute("download", r.exportFilename + ".png"), o.appendChild(d), o.appendChild(c);
	    }r.pan && (o.style.overflow = "hidden", a.addEventListener("mousedown", this._onPanStart.bind(this)), a.addEventListener("touchstart", this._onPanStart.bind(this)), document.body.addEventListener("mouseup", this._onPanEnd.bind(this)), document.body.addEventListener("touchend", this._onPanEnd.bind(this))), r.zoom && (o.addEventListener("wheel", this._onWheeling.bind(this)), o.addEventListener("touchstart", this._onTouchStart.bind(this)), document.body.addEventListener("touchmove", this._onTouchMove.bind(this)), document.body.addEventListener("touchend", this._onTouchEnd.bind(this))), o.appendChild(a);
	  }return _createClass(e, [{ key: "_closest", value: function value(e, t) {
	      return e && (t(e) && e !== this.chart ? e : this._closest(e.parentNode, t));
	    } }, { key: "_siblings", value: function value(e, t) {
	      return Array.from(e.parentNode.children).filter(function (n) {
	        return n !== e && (!t || e.matches(t));
	      });
	    } }, { key: "_prevAll", value: function value(e, t) {
	      for (var n = [], i = e.previousElementSibling; i;) {
	        t && !i.matches(t) || n.push(i), i = i.previousElementSibling;
	      }return n;
	    } }, { key: "_nextAll", value: function value(e, t) {
	      for (var n = [], i = e.nextElementSibling; i;) {
	        t && !i.matches(t) || n.push(i), i = i.nextElementSibling;
	      }return n;
	    } }, { key: "_isVisible", value: function value(e) {
	      return null !== e.offsetParent;
	    } }, { key: "_addClass", value: function value(e, t) {
	      e.forEach(function (e) {
	        t.indexOf(" ") > 0 ? t.split(" ").forEach(function (t) {
	          return e.classList.add(t);
	        }) : e.classList.add(t);
	      });
	    } }, { key: "_removeClass", value: function value(e, t) {
	      e.forEach(function (e) {
	        t.indexOf(" ") > 0 ? t.split(" ").forEach(function (t) {
	          return e.classList.remove(t);
	        }) : e.classList.remove(t);
	      });
	    } }, { key: "_css", value: function value(e, t, n) {
	      e.forEach(function (e) {
	        e.style[t] = n;
	      });
	    } }, { key: "_removeAttr", value: function value(e, t) {
	      e.forEach(function (e) {
	        e.removeAttribute(t);
	      });
	    } }, { key: "_one", value: function value(e, t, n, i) {
	      var r = function r(s) {
	        try {
	          n.call(i, s);
	        } finally {
	          e.removeEventListener(t, r);
	        }
	      };e.addEventListener(t, r);
	    } }, { key: "_getDescElements", value: function value(e, t) {
	      var n = [];return e.forEach(function (e) {
	        return n.push.apply(n, _toConsumableArray(e.querySelectorAll(t)));
	      }), n;
	    } }, { key: "_getJSON", value: function value(e) {
	      return new Promise(function (t, n) {
	        function i() {
	          4 === this.readyState && (200 === this.status ? t(JSON.parse(this.response)) : n(new Error(this.statusText)));
	        }var r = new XMLHttpRequest();r.open("GET", e), r.onreadystatechange = i, r.responseType = "json", r.setRequestHeader("Content-Type", "application/json"), r.send();
	      });
	    } }, { key: "_buildJsonDS", value: function value(e) {
	      var t = this,
	          n = { name: e.firstChild.textContent.trim(), relationship: ("LI" === e.parentNode.parentNode.nodeName ? "1" : "0") + (e.parentNode.children.length > 1 ? 1 : 0) + (e.children.length ? 1 : 0) };return e.id && (n.id = e.id), e.querySelector("ul") && Array.from(e.querySelector("ul").children).forEach(function (e) {
	        n.children || (n.children = []), n.children.push(t._buildJsonDS(e));
	      }), n;
	    } }, { key: "_attachRel", value: function value(e, t) {
	      if (e.relationship = t + (e.children && e.children.length > 0 ? 1 : 0), e.children) {
	        var n = !0,
	            i = !1,
	            r = void 0;try {
	          for (var s, a = e.children[Symbol.iterator](); !(n = (s = a.next()).done); n = !0) {
	            var o = s.value;this._attachRel(o, "1" + (e.children.length > 1 ? 1 : 0));
	          }
	        } catch (e) {
	          i = !0, r = e;
	        } finally {
	          try {
	            !n && a.return && a.return();
	          } finally {
	            if (i) throw r;
	          }
	        }
	      }return e;
	    } }, { key: "_repaint", value: function value(e) {
	      e && (e.style.offsetWidth = e.offsetWidth);
	    } }, { key: "_isInAction", value: function value(e) {
	      return e.querySelector(":scope > .edge").className.indexOf("fa-") > -1;
	    } }, { key: "_getNodeState", value: function value(e, t) {
	      var n = this,
	          i = void 0,
	          r = { exist: !1, visible: !1 };return "parent" === t ? (i = this._closest(e, function (e) {
	        return e.classList && e.classList.contains("nodes");
	      }), i && (r.exist = !0), r.exist && this._isVisible(i.parentNode.children[0]) && (r.visible = !0)) : "children" === t ? (i = this._closest(e, function (e) {
	        return "TR" === e.nodeName;
	      }).nextElementSibling, i && (r.exist = !0), r.exist && this._isVisible(i) && (r.visible = !0)) : "siblings" === t && (i = this._siblings(this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }).parentNode), i.length && (r.exist = !0), r.exist && i.some(function (e) {
	        return n._isVisible(e);
	      }) && (r.visible = !0)), r;
	    } }, { key: "getRelatedNodes", value: function value(e, t) {
	      return "parent" === t ? this._closest(e, function (e) {
	        return e.classList.contains("nodes");
	      }).parentNode.children[0].querySelector(".node") : "children" === t ? Array.from(this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }).lastChild.children).map(function (e) {
	        return e.querySelector(".node");
	      }) : "siblings" === t ? this._siblings(this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }).parentNode).map(function (e) {
	        return e.querySelector(".node");
	      }) : [];
	    } }, { key: "_switchHorizontalArrow", value: function value(e) {
	      var t = this.options,
	          n = e.querySelector(".leftEdge"),
	          i = e.querySelector(".rightEdge"),
	          r = this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }).parentNode;if (t.toggleSiblingsResp && ("undefined" == typeof t.ajaxURL || this._closest(e, function (e) {
	        return e.classList.contains(".nodes");
	      }).dataset.siblingsLoaded)) {
	        var s = r.previousElementSibling,
	            a = r.nextElementSibling;s && (s.classList.contains("hidden") ? (n.classList.add("fa-chevron-left"), n.classList.remove("fa-chevron-right")) : (n.classList.add("fa-chevron-right"), n.classList.remove("fa-chevron-left"))), a && (a.classList.contains("hidden") ? (i.classList.add("fa-chevron-right"), i.classList.remove("fa-chevron-left")) : (i.classList.add("fa-chevron-left"), i.classList.remove("fa-chevron-right")));
	      } else {
	        var o = this._siblings(r),
	            l = !!o.length && !o.some(function (e) {
	          return e.classList.contains("hidden");
	        });n.classList.toggle("fa-chevron-right", l), n.classList.toggle("fa-chevron-left", !l), i.classList.toggle("fa-chevron-left", l), i.classList.toggle("fa-chevron-right", !l);
	      }
	    } }, { key: "_hoverNode", value: function value(e) {
	      var t = e.target,
	          n = !1,
	          i = t.querySelector(":scope > .topEdge"),
	          r = t.querySelector(":scope > .bottomEdge"),
	          s = t.querySelector(":scope > .leftEdge");"mouseenter" === e.type ? (i && (n = this._getNodeState(t, "parent").visible, i.classList.toggle("fa-chevron-up", !n), i.classList.toggle("fa-chevron-down", n)), r && (n = this._getNodeState(t, "children").visible, r.classList.toggle("fa-chevron-down", !n), r.classList.toggle("fa-chevron-up", n)), s && this._switchHorizontalArrow(t)) : Array.from(t.querySelectorAll(":scope > .edge")).forEach(function (e) {
	        e.classList.remove("fa-chevron-up", "fa-chevron-down", "fa-chevron-right", "fa-chevron-left");
	      });
	    } }, { key: "_clickNode", value: function value(e) {
	      var t = e.currentTarget,
	          n = this.chart.querySelector(".focused");n && n.classList.remove("focused"), t.classList.add("focused");
	    } }, { key: "_buildParentNode", value: function value(e, t, n) {
	      var i = this,
	          r = document.createElement("table");t.relationship = "001", this._createNode(t, 0).then(function (e) {
	        var t = i.chart;e.classList.remove("slide-up"), e.classList.add("slide-down");var s = document.createElement("tr"),
	            a = document.createElement("tr"),
	            o = document.createElement("tr"),
	            l = document.createElement("tr");s.setAttribute("class", "hidden"), s.innerHTML = '<td colspan="2"></td>', r.appendChild(s), a.setAttribute("class", "lines hidden"), a.innerHTML = '<td colspan="2"><div class="downLine"></div></td>', r.appendChild(a), o.setAttribute("class", "lines hidden"), o.innerHTML = '<td class="rightLine">&nbsp;</td><td class="leftLine">&nbsp;</td>', r.appendChild(o), l.setAttribute("class", "nodes"), l.innerHTML = '<td colspan="2"></td>', r.appendChild(l), r.querySelector("td").appendChild(e), t.insertBefore(r, t.children[0]), r.children[3].children[0].appendChild(t.lastChild), n();
	      }).catch(function (e) {
	        console.error("Failed to create parent node", e);
	      });
	    } }, { key: "_switchVerticalArrow", value: function value(e) {
	      e.classList.toggle("fa-chevron-up"), e.classList.toggle("fa-chevron-down");
	    } }, { key: "showParent", value: function value(e) {
	      var t = this._prevAll(this._closest(e, function (e) {
	        return e.classList.contains("nodes");
	      }));this._removeClass(t, "hidden"), this._addClass(Array(t[0].children).slice(1, -1), "hidden");var n = t[2].querySelector(".node");this._one(n, "transitionend", function () {
	        n.classList.remove("slide"), this._isInAction(e) && this._switchVerticalArrow(e.querySelector(":scope > .topEdge"));
	      }, this), this._repaint(n), n.classList.add("slide"), n.classList.remove("slide-down");
	    } }, { key: "showSiblings", value: function value(e, t) {
	      var n = this,
	          i = [],
	          r = this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }).parentNode;i = t ? "left" === t ? this._prevAll(r) : this._nextAll(r) : this._siblings(r), this._removeClass(i, "hidden");var s = this._prevAll(this._closest(e, function (e) {
	        return e.classList.contains("nodes");
	      }));if (r = Array.from(s[0].querySelectorAll(":scope > .hidden")), t ? this._removeClass(r.slice(0, 2 * i.length), "hidden") : this._removeClass(r, "hidden"), !this._getNodeState(e, "parent").visible) {
	        this._removeClass(s, "hidden");var a = s[2].querySelector(".node");this._one(a, "transitionend", function (e) {
	          e.target.classList.remove("slide");
	        }, this), this._repaint(a), a.classList.add("slide"), a.classList.remove("slide-down");
	      }i.forEach(function (e) {
	        Array.from(e.querySelectorAll(".node")).forEach(function (e) {
	          n._isVisible(e) && (e.classList.add("slide"), e.classList.remove("slide-left", "slide-right"));
	        });
	      }), this._one(i[0].querySelector(".slide"), "transitionend", function () {
	        var t = this;i.forEach(function (e) {
	          t._removeClass(Array.from(e.querySelectorAll(".slide")), "slide");
	        }), this._isInAction(e) && (this._switchHorizontalArrow(e), e.querySelector(".topEdge").classList.remove("fa-chevron-up"), e.querySelector(".topEdge").classList.add("fa-chevron-down"));
	      }, this);
	    } }, { key: "hideSiblings", value: function value(e, t) {
	      var n = this,
	          i = this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }).parentNode,
	          r = this._siblings(i);if (r.forEach(function (e) {
	        e.querySelector(".spinner") && (n.chart.dataset.inAjax = !1);
	      }), !t || t && "left" === t) {
	        var s = this._prevAll(i);s.forEach(function (e) {
	          Array.from(e.querySelectorAll(".node")).forEach(function (e) {
	            n._isVisible(e) && e.classList.add("slide", "slide-right");
	          });
	        });
	      }if (!t || t && "left" !== t) {
	        var a = this._nextAll(i);a.forEach(function (e) {
	          Array.from(e.querySelectorAll(".node")).forEach(function (e) {
	            n._isVisible(e) && e.classList.add("slide", "slide-left");
	          });
	        });
	      }var o = [];this._siblings(i).forEach(function (e) {
	        Array.prototype.push.apply(o, Array.from(e.querySelectorAll(".slide")));
	      });var l = [],
	          d = !0,
	          c = !1,
	          h = void 0;try {
	        for (var u, f = o[Symbol.iterator](); !(d = (u = f.next()).done); d = !0) {
	          var p = u.value,
	              v = this._closest(p, function (e) {
	            return e.classList.contains("nodes");
	          }).previousElementSibling;l.push(v), l.push(v.previousElementSibling);
	        }
	      } catch (e) {
	        c = !0, h = e;
	      } finally {
	        try {
	          !d && f.return && f.return();
	        } finally {
	          if (c) throw h;
	        }
	      }l = [].concat(_toConsumableArray(new Set(l))), l.forEach(function (e) {
	        e.style.visibility = "hidden";
	      }), this._one(o[0], "transitionend", function (n) {
	        var r = this;l.forEach(function (e) {
	          e.removeAttribute("style");
	        });var s = [];s = t ? "left" === t ? this._prevAll(i, ":not(.hidden)") : this._nextAll(i, ":not(.hidden)") : this._siblings(i);var a = Array.from(this._closest(i, function (e) {
	          return e.classList.contains("nodes");
	        }).previousElementSibling.querySelectorAll(":scope > :not(.hidden)")),
	            d = a.slice(1, t ? 2 * s.length + 1 : -1);this._addClass(d, "hidden"), this._removeClass(o, "slide"), s.forEach(function (e) {
	          Array.from(e.querySelectorAll(".node")).slice(1).forEach(function (e) {
	            r._isVisible(e) && (e.classList.remove("slide-left", "slide-right"), e.classList.add("slide-up"));
	          });
	        }), s.forEach(function (e) {
	          r._addClass(Array.from(e.querySelectorAll(".lines")), "hidden"), r._addClass(Array.from(e.querySelectorAll(".nodes")), "hidden"), r._addClass(Array.from(e.querySelectorAll(".verticalNodes")), "hidden");
	        }), this._addClass(s, "hidden"), this._isInAction(e) && this._switchHorizontalArrow(e);
	      }, this);
	    } }, { key: "hideParent", value: function value(e) {
	      var t = Array.from(this._closest(e, function (e) {
	        return e.classList.contains("nodes");
	      }).parentNode.children).slice(0, 3);t[0].querySelector(".spinner") && (this.chart.dataset.inAjax = !1), this._getNodeState(e, "siblings").visible && this.hideSiblings(e);var n = t.slice(1);this._css(n, "visibility", "hidden");var i = t[0].querySelector(".node"),
	          r = this._getNodeState(i, "parent").visible;i && this._isVisible(i) && (i.classList.add("slide", "slide-down"), this._one(i, "transitionend", function () {
	        i.classList.remove("slide"), this._removeAttr(n, "style"), this._addClass(t, "hidden");
	      }, this)), i && r && this.hideParent(i);
	    } }, { key: "addParent", value: function value(e, t) {
	      var n = this;this._buildParentNode(e, t, function () {
	        if (!e.querySelector(":scope > .topEdge")) {
	          var t = document.createElement("i");t.setAttribute("class", "edge verticalEdge topEdge fa"), e.appendChild(t);
	        }n.showParent(e);
	      });
	    } }, { key: "_startLoading", value: function value(e, t) {
	      var n = this.options,
	          i = this.chart;if ("undefined" != typeof i.dataset.inAjax && "true" === i.dataset.inAjax) return !1;e.classList.add("hidden");var r = document.createElement("i");r.setAttribute("class", "fa fa-circle-o-notch fa-spin spinner"), t.appendChild(r), this._addClass(Array.from(t.querySelectorAll(":scope > *:not(.spinner)")), "hazy"), i.dataset.inAjax = !0;var s = this.chartContainer.querySelector(".oc-export-btn" + ("" !== n.chartClass ? "." + n.chartClass : ""));return s && (s.disabled = !0), !0;
	    } }, { key: "_endLoading", value: function value(e, t) {
	      var n = this.options;e.classList.remove("hidden"), t.querySelector(":scope > .spinner").remove(), this._removeClass(Array.from(t.querySelectorAll(":scope > .hazy")), "hazy"), this.chart.dataset.inAjax = !1;var i = this.chartContainer.querySelector(".oc-export-btn" + ("" !== n.chartClass ? "." + n.chartClass : ""));i && (i.disabled = !1);
	    } }, { key: "_clickTopEdge", value: function value(e) {
	      e.stopPropagation();var t = this,
	          n = e.target,
	          i = n.parentNode,
	          r = this._getNodeState(i, "parent"),
	          s = this.options;if (r.exist) {
	        var a = this._closest(i, function (e) {
	          return e.classList.contains("nodes");
	        }),
	            o = a.parentNode.firstChild.querySelector(".node");if (o.classList.contains("slide")) return;r.visible ? (this.hideParent(i), this._one(o, "transitionend", function () {
	          this._isInAction(i) && (this._switchVerticalArrow(n), this._switchHorizontalArrow(i));
	        }, this)) : this.showParent(i);
	      } else {
	        var l = n.parentNode.id;this._startLoading(n, i) && this._getJSON("function" == typeof s.ajaxURL.parent ? s.ajaxURL.parent(i.dataset.source) : s.ajaxURL.parent + l).then(function (e) {
	          "true" === t.chart.dataset.inAjax && Object.keys(e).length && t.addParent(i, e);
	        }).catch(function (e) {
	          console.error("Failed to get parent node data.", e);
	        }).finally(function () {
	          t._endLoading(n, i);
	        });
	      }
	    } }, { key: "hideChildren", value: function value(e) {
	      var t = this,
	          n = this._nextAll(e.parentNode.parentNode),
	          i = n[n.length - 1],
	          r = [];i.querySelector(".spinner") && (this.chart.dataset.inAjax = !1);var s = Array.from(i.querySelectorAll(".node")).filter(function (e) {
	        return t._isVisible(e);
	      }),
	          a = i.classList.contains("verticalNodes");a || (s.forEach(function (e) {
	        Array.prototype.push.apply(r, t._prevAll(t._closest(e, function (e) {
	          return e.classList.contains("nodes");
	        }), ".lines"));
	      }), r = [].concat(_toConsumableArray(new Set(r))), this._css(r, "visibility", "hidden")), this._one(s[0], "transitionend", function (o) {
	        this._removeClass(s, "slide"), a ? t._addClass(n, "hidden") : (r.forEach(function (e) {
	          e.removeAttribute("style"), e.classList.add("hidden"), e.parentNode.lastChild.classList.add("hidden");
	        }), this._addClass(Array.from(i.querySelectorAll(".verticalNodes")), "hidden")), this._isInAction(e) && this._switchVerticalArrow(e.querySelector(".bottomEdge"));
	      }, this), this._addClass(s, "slide slide-up");
	    } }, { key: "showChildren", value: function value(e) {
	      var t = this,
	          n = this,
	          i = this._nextAll(e.parentNode.parentNode),
	          r = [];this._removeClass(i, "hidden"), i.some(function (e) {
	        return e.classList.contains("verticalNodes");
	      }) ? i.forEach(function (e) {
	        Array.prototype.push.apply(r, Array.from(e.querySelectorAll(".node")).filter(function (e) {
	          return n._isVisible(e);
	        }));
	      }) : Array.from(i[2].children).forEach(function (e) {
	        Array.prototype.push.apply(r, Array.from(e.querySelector("tr").querySelectorAll(".node")).filter(function (e) {
	          return n._isVisible(e);
	        }));
	      }), this._repaint(r[0]), this._one(r[0], "transitionend", function (n) {
	        t._removeClass(r, "slide"), t._isInAction(e) && t._switchVerticalArrow(e.querySelector(".bottomEdge"));
	      }, this), this._addClass(r, "slide"), this._removeClass(r, "slide-up");
	    } }, { key: "_buildChildNode", value: function value(e, t, n) {
	      var i = t.children || t.siblings;e.querySelector("td").setAttribute("colSpan", 2 * i.length), this.buildHierarchy(e, { children: i }, 0, n);
	    } }, { key: "addChildren", value: function value(e, t) {
	      var n = this,
	          i = this.options,
	          r = 0;this.chart.dataset.inEdit = "addChildren", this._buildChildNode.call(this, this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }), t, function () {
	        if (++r === t.children.length) {
	          if (!e.querySelector(".bottomEdge")) {
	            var s = document.createElement("i");s.setAttribute("class", "edge verticalEdge bottomEdge fa"), e.appendChild(s);
	          }if (!e.querySelector(".symbol")) {
	            var a = document.createElement("i");a.setAttribute("class", "fa " + i.parentNodeSymbol + " symbol"), e.querySelector(":scope > .title").appendChild(a);
	          }n.showChildren(e), n.chart.dataset.inEdit = "";
	        }
	      });
	    } }, { key: "_clickBottomEdge", value: function value(e) {
	      var t = this;e.stopPropagation();var n = this,
	          i = this.options,
	          r = e.target,
	          s = r.parentNode,
	          a = this._getNodeState(s, "children");if (a.exist) {
	        var o = this._closest(s, function (e) {
	          return "TR" === e.nodeName;
	        }).parentNode.lastChild;if (Array.from(o.querySelectorAll(".node")).some(function (e) {
	          return t._isVisible(e) && e.classList.contains("slide");
	        })) return;a.visible ? this.hideChildren(s) : this.showChildren(s);
	      } else {
	        var l = r.parentNode.id;this._startLoading(r, s) && this._getJSON("function" == typeof i.ajaxURL.children ? i.ajaxURL.children(s.dataset.source) : i.ajaxURL.children + l).then(function (e) {
	          "true" === n.chart.dataset.inAjax && e.children.length && n.addChildren(s, e);
	        }).catch(function (e) {
	          console.error("Failed to get children nodes data", e);
	        }).finally(function () {
	          n._endLoading(r, s);
	        });
	      }
	    } }, { key: "_complementLine", value: function value(e, t, n) {
	      var i = e.parentNode.parentNode.children;i[0].children[0].setAttribute("colspan", 2 * t), i[1].children[0].setAttribute("colspan", 2 * t);for (var r = 0; r < n; r++) {
	        var s = document.createElement("td"),
	            a = document.createElement("td");s.setAttribute("class", "rightLine topLine"), s.innerHTML = "&nbsp;", i[2].insertBefore(s, i[2].children[1]), a.setAttribute("class", "leftLine topLine"), a.innerHTML = "&nbsp;", i[2].insertBefore(a, i[2].children[1]);
	      }
	    } }, { key: "_buildSiblingNode", value: function value(e, t, n) {
	      var i = this,
	          r = this,
	          s = t.siblings ? t.siblings.length : t.children.length,
	          a = "TD" === e.parentNode.nodeName ? this._closest(e, function (e) {
	        return "TR" === e.nodeName;
	      }).children.length : 1,
	          o = a + s,
	          l = o > 1 ? Math.floor(o / 2 - 1) : 0;"TD" === e.parentNode.nodeName ? !function () {
	        var d = i._prevAll(e.parentNode.parentNode);d[0].remove(), d[1].remove();var c = 0;r._buildChildNode.call(r, r._closest(e.parentNode, function (e) {
	          return "TABLE" === e.nodeName;
	        }), t, function () {
	          ++c === s && !function () {
	            var t = Array.from(r._closest(e.parentNode, function (e) {
	              return "TABLE" === e.nodeName;
	            }).lastChild.children);if (a > 1) {
	              var i = e.parentNode.parentNode;Array.from(i.children).forEach(function (e) {
	                t[0].parentNode.insertBefore(e, t[0]);
	              }), i.remove(), r._complementLine(t[0], o, a), r._addClass(t, "hidden"), t.forEach(function (e) {
	                r._addClass(e.querySelectorAll(".node"), "slide-left");
	              });
	            } else {
	              var s = e.parentNode.parentNode;t[l].parentNode.insertBefore(e.parentNode, t[l + 1]), s.remove(), r._complementLine(t[l], o, 1), r._addClass(t, "hidden"), r._addClass(r._getDescElements(t.slice(0, l + 1), ".node"), "slide-right"), r._addClass(r._getDescElements(t.slice(l + 1), ".node"), "slide-left");
	            }n();
	          }();
	        });
	      }() : !function () {
	        var s = 0;r.buildHierarchy.call(r, r.chart, t, 0, function () {
	          if (++s === o) {
	            var t = e.nextElementSibling.children[3].children[l],
	                a = document.createElement("td");a.setAttribute("colspan", 2), a.appendChild(e), t.parentNode.insertBefore(a, t.nextElementSibling), r._complementLine(t, o, 1);var d = r._closest(e, function (e) {
	              return e.classList && e.classList.contains("nodes");
	            }).parentNode.children[0];d.classList.add("hidden"), r._addClass(Array.from(d.querySelectorAll(".node")), "slide-down");var c = i._siblings(e.parentNode);r._addClass(c, "hidden"), r._addClass(r._getDescElements(c.slice(0, l), ".node"), "slide-right"), r._addClass(r._getDescElements(c.slice(l), ".node"), "slide-left"), n();
	          }
	        });
	      }();
	    } }, { key: "addSiblings", value: function value(e, t) {
	      var n = this;this.chart.dataset.inEdit = "addSiblings", this._buildSiblingNode.call(this, this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }), t, function () {
	        if (n._closest(e, function (e) {
	          return e.classList && e.classList.contains("nodes");
	        }).dataset.siblingsLoaded = !0, !e.querySelector(".leftEdge")) {
	          var t = document.createElement("i"),
	              i = document.createElement("i");t.setAttribute("class", "edge horizontalEdge rightEdge fa"), e.appendChild(t), i.setAttribute("class", "edge horizontalEdge leftEdge fa"), e.appendChild(i);
	        }n.showSiblings(e), n.chart.dataset.inEdit = "";
	      });
	    } }, { key: "removeNodes", value: function value(e) {
	      var t = this._closest(e, function (e) {
	        return "TABLE" === e.nodeName;
	      }).parentNode,
	          n = this._siblings(t.parentNode);"TD" === t.nodeName ? this._getNodeState(e, "siblings").exist ? (n[2].querySelector(".topLine").nextElementSibling.remove(), n[2].querySelector(".topLine").remove(), n[0].children[0].setAttribute("colspan", n[2].children.length), n[1].children[0].setAttribute("colspan", n[2].children.length), t.remove()) : (n[0].children[0].removeAttribute("colspan"), n[0].querySelector(".bottomEdge").remove(), this._siblings(n[0]).forEach(function (e) {
	        return e.remove();
	      })) : Array.from(t.parentNode.children).forEach(function (e) {
	        return e.remove();
	      });
	    } }, { key: "_clickHorizontalEdge", value: function value(e) {
	      var t = this;e.stopPropagation();var n = this,
	          i = this.options,
	          r = e.target,
	          s = r.parentNode,
	          a = this._getNodeState(s, "siblings");if (a.exist) {
	        var o = this._closest(s, function (e) {
	          return "TABLE" === e.nodeName;
	        }).parentNode,
	            l = this._siblings(o);if (l.some(function (e) {
	          var n = e.querySelector(".node");return t._isVisible(n) && n.classList.contains("slide");
	        })) return;if (i.toggleSiblingsResp) {
	          var d = this._closest(s, function (e) {
	            return "TABLE" === e.nodeName;
	          }).parentNode.previousElementSibling,
	              c = this._closest(s, function (e) {
	            return "TABLE" === e.nodeName;
	          }).parentNode.nextElementSibling;r.classList.contains("leftEdge") ? d.classList.contains("hidden") ? this.showSiblings(s, "left") : this.hideSiblings(s, "left") : c.classList.contains("hidden") ? this.showSiblings(s, "right") : this.hideSiblings(s, "right");
	        } else a.visible ? this.hideSiblings(s) : this.showSiblings(s);
	      } else {
	        var h = r.parentNode.id,
	            u = this._getNodeState(s, "parent").exist ? "function" == typeof i.ajaxURL.siblings ? i.ajaxURL.siblings(JSON.parse(s.dataset.source)) : i.ajaxURL.siblings + h : "function" == typeof i.ajaxURL.families ? i.ajaxURL.families(JSON.parse(s.dataset.source)) : i.ajaxURL.families + h;this._startLoading(r, s) && this._getJSON(u).then(function (e) {
	          "true" === n.chart.dataset.inAjax && (e.siblings || e.children) && n.addSiblings(s, e);
	        }).catch(function (e) {
	          console.error("Failed to get sibling nodes data", e);
	        }).finally(function () {
	          n._endLoading(r, s);
	        });
	      }
	    } }, { key: "_clickToggleButton", value: function value(e) {
	      var t = this,
	          n = e.target,
	          i = n.parentNode.nextElementSibling,
	          r = Array.from(i.querySelectorAll(".node")),
	          s = Array.from(i.children).map(function (e) {
	        return e.querySelector(".node");
	      });s.some(function (e) {
	        return e.classList.contains("slide");
	      }) || (n.classList.toggle("fa-plus-square"), n.classList.toggle("fa-minus-square"), r[0].classList.contains("slide-up") ? (i.classList.remove("hidden"), this._repaint(s[0]), this._addClass(s, "slide"), this._removeClass(s, "slide-up"), this._one(s[0], "transitionend", function () {
	        t._removeClass(s, "slide");
	      })) : (this._addClass(r, "slide slide-up"), this._one(r[0], "transitionend", function () {
	        t._removeClass(r, "slide"), r.forEach(function (e) {
	          var n = t._closest(e, function (e) {
	            return "UL" === e.nodeName;
	          });n.classList.add("hidden");
	        });
	      }), r.forEach(function (e) {
	        var n = Array.from(e.querySelectorAll(".toggleBtn"));t._removeClass(n, "fa-minus-square"), t._addClass(n, "fa-plus-square");
	      })));
	    } }, { key: "_dispatchClickEvent", value: function value(e) {
	      var t = e.target.classList;t.contains("topEdge") ? this._clickTopEdge(e) : t.contains("rightEdge") || t.contains("leftEdge") ? this._clickHorizontalEdge(e) : t.contains("bottomEdge") ? this._clickBottomEdge(e) : t.contains("toggleBtn") ? this._clickToggleButton(e) : this._clickNode(e);
	    } }, { key: "_onDragStart", value: function value(e) {
	      var t = e.target,
	          n = this.options,
	          i = /firefox/.test(window.navigator.userAgent.toLowerCase());if (i && e.dataTransfer.setData("text/html", "hack for firefox"), this.chart.style.transform) {
	        var r = void 0,
	            s = void 0;document.querySelector(".ghost-node") ? (r = this.chart.querySelector(":scope > .ghost-node"), s = r.children[0]) : (r = document.createElementNS("http://www.w3.org/2000/svg", "svg"), r.classList.add("ghost-node"), s = document.createElementNS("http://www.w3.org/2000/svg", "rect"), r.appendChild(s), this.chart.appendChild(r));var a = this.chart.style.transform.split(","),
	            o = Math.abs(window.parseFloat("t2b" === n.direction || "b2t" === n.direction ? a[0].slice(a[0].indexOf("(") + 1) : a[1]));r.setAttribute("width", t.offsetWidth), r.setAttribute("height", t.offsetHeight), s.setAttribute("x", 5 * o), s.setAttribute("y", 5 * o), s.setAttribute("width", 120 * o), s.setAttribute("height", 40 * o), s.setAttribute("rx", 4 * o), s.setAttribute("ry", 4 * o), s.setAttribute("stroke-width", 1 * o);var l = e.offsetX * o,
	            d = e.offsetY * o;if ("l2r" === n.direction ? (l = e.offsetY * o, d = e.offsetX * o) : "r2l" === n.direction ? (l = t.offsetWidth - e.offsetY * o, d = e.offsetX * o) : "b2t" === n.direction && (l = t.offsetWidth - e.offsetX * o, d = t.offsetHeight - e.offsetY * o), i) {
	          var c = document.createElement("img");c.src = "data:image/svg+xml;utf8," + new XMLSerializer().serializeToString(r), e.dataTransfer.setDragImage(c, l, d), s.setAttribute("fill", "rgb(255, 255, 255)"), s.setAttribute("stroke", "rgb(191, 0, 0)");
	        } else e.dataTransfer.setDragImage(r, l, d);
	      }var h = e.target,
	          u = this._closest(h, function (e) {
	        return e.classList && e.classList.contains("nodes");
	      }).parentNode.children[0].querySelector(".node"),
	          f = Array.from(this._closest(h, function (e) {
	        return "TABLE" === e.nodeName;
	      }).querySelectorAll(".node"));this.dragged = h, Array.from(this.chart.querySelectorAll(".node")).forEach(function (e) {
	        f.includes(e) || (n.dropCriteria ? n.dropCriteria(h, u, e) && e.classList.add("allowedDrop") : e.classList.add("allowedDrop"));
	      });
	    } }, { key: "_onDragOver", value: function value(e) {
	      e.preventDefault();var t = e.currentTarget;t.classList.contains("allowedDrop") || (e.dataTransfer.dropEffect = "none");
	    } }, { key: "_onDragEnd", value: function value(e) {
	      Array.from(this.chart.querySelectorAll(".allowedDrop")).forEach(function (e) {
	        e.classList.remove("allowedDrop");
	      });
	    } }, { key: "_onDrop", value: function value(e) {
	      var t = e.currentTarget,
	          n = this.chart,
	          i = this.dragged,
	          r = this._closest(i, function (e) {
	        return e.classList && e.classList.contains("nodes");
	      }).parentNode.children[0].children[0];if (this._removeClass(Array.from(n.querySelectorAll(".allowedDrop")), "allowedDrop"), t.parentNode.parentNode.nextElementSibling) {
	        var s = window.parseInt(t.parentNode.colSpan) + 2;if (t.parentNode.setAttribute("colspan", s), t.parentNode.parentNode.nextElementSibling.children[0].setAttribute("colspan", s), !i.querySelector(".horizontalEdge")) {
	          var a = document.createElement("i"),
	              o = document.createElement("i");a.setAttribute("class", "edge horizontalEdge rightEdge fa"), i.appendChild(a), o.setAttribute("class", "edge horizontalEdge leftEdge fa"), i.appendChild(o);
	        }var l = t.parentNode.parentNode.nextElementSibling.nextElementSibling,
	            d = document.createElement("td"),
	            c = document.createElement("td");d.setAttribute("class", "leftLine topLine"), d.innerHTML = "&nbsp;", l.insertBefore(d, l.children[1]), c.setAttribute("class", "rightLine topLine"), c.innerHTML = "&nbsp;", l.insertBefore(c, l.children[2]), l.nextElementSibling.appendChild(this._closest(i, function (e) {
	          return "TABLE" === e.nodeName;
	        }).parentNode);var h = this._siblings(this._closest(i, function (e) {
	          return "TABLE" === e.nodeName;
	        }).parentNode).map(function (e) {
	          return e.querySelector(".node");
	        });if (1 === h.length) {
	          var u = document.createElement("i"),
	              f = document.createElement("i");u.setAttribute("class", "edge horizontalEdge rightEdge fa"), h[0].appendChild(u), f.setAttribute("class", "edge horizontalEdge leftEdge fa"), h[0].appendChild(f);
	        }
	      } else {
	        var p = document.createElement("i");p.setAttribute("class", "edge verticalEdge bottomEdge fa"), t.appendChild(p), t.parentNode.setAttribute("colspan", 2);var v = this._closest(t, function (e) {
	          return "TABLE" === e.nodeName;
	        }),
	            g = document.createElement("tr"),
	            m = document.createElement("tr"),
	            y = document.createElement("tr");g.setAttribute("class", "lines"), g.innerHTML = '<td colspan="2"><div class="downLine"></div></td>', v.appendChild(g), m.setAttribute("class", "lines"), m.innerHTML = '<td class="rightLine">&nbsp;</td><td class="leftLine">&nbsp;</td>', v.appendChild(m), y.setAttribute("class", "nodes"), v.appendChild(y), Array.from(i.querySelectorAll(".horizontalEdge")).forEach(function (e) {
	          i.removeChild(e);
	        });var b = this._closest(i, function (e) {
	          return "TABLE" === e.nodeName;
	        }).parentNode;y.appendChild(b);
	      }var _ = window.parseInt(r.colSpan);if (_ > 2) {
	        r.setAttribute("colspan", _ - 2), r.parentNode.nextElementSibling.children[0].setAttribute("colspan", _ - 2);var E = r.parentNode.nextElementSibling.nextElementSibling;E.children[1].remove(), E.children[1].remove();var L = Array.from(r.parentNode.parentNode.children[3].children).map(function (e) {
	          return e.querySelector(".node");
	        });1 === L.length && (L[0].querySelector(".leftEdge").remove(), L[0].querySelector(".rightEdge").remove());
	      } else r.removeAttribute("colspan"), r.querySelector(".node").removeChild(r.querySelector(".bottomEdge")), Array.from(r.parentNode.parentNode.children).slice(1).forEach(function (e) {
	        return e.remove();
	      });var A = new CustomEvent("nodedropped.orgchart", { detail: { draggedNode: i, dragZone: r.children[0], dropZone: t } });n.dispatchEvent(A);
	    } }, { key: "_createNode", value: function value(e, t) {
	      var n = this,
	          i = this.options;return new Promise(function (r, s) {
	        if (e.children) {
	          var a = !0,
	              o = !1,
	              l = void 0;try {
	            for (var d, c = e.children[Symbol.iterator](); !(a = (d = c.next()).done); a = !0) {
	              var h = d.value;h.parentId = e.id;
	            }
	          } catch (e) {
	            o = !0, l = e;
	          } finally {
	            try {
	              !a && c.return && c.return();
	            } finally {
	              if (o) throw l;
	            }
	          }
	        }var u = document.createElement("div");delete e.children, u.dataset.source = JSON.stringify(e), e[i.nodeId] && (u.id = e[i.nodeId]);var f = n.chart.dataset.inEdit,
	            p = void 0;p = f ? "addChildren" === f ? " slide-up" : "" : t >= i.depth ? " slide-up" : "", u.setAttribute("class", "node " + (e.className || "") + p), i.draggable && u.setAttribute("draggable", !0), e.parentId && u.setAttribute("data-parent", e.parentId), u.innerHTML = '\n        <div class="title">' + e[i.nodeTitle] + "</div>\n        " + (i.nodeContent ? '<div class="content">' + e[i.nodeContent] + "</div>" : "") + "\n      ";
	        var v = e.relationship || "";if (i.verticalDepth && t + 2 > i.verticalDepth) {
	          if (t + 1 >= i.verticalDepth && Number(v.substr(2, 1))) {
	            var g = document.createElement("i"),
	                m = t + 1 >= i.depth ? "plus" : "minus";g.setAttribute("class", "toggleBtn fa fa-" + m + "-square"), u.appendChild(g);
	          }
	        } else {
	          if (Number(v.substr(0, 1))) {
	            var y = document.createElement("i");y.setAttribute("class", "edge verticalEdge topEdge fa"), u.appendChild(y);
	          }if (Number(v.substr(1, 1))) {
	            var b = document.createElement("i"),
	                _ = document.createElement("i");b.setAttribute("class", "edge horizontalEdge rightEdge fa"), u.appendChild(b), _.setAttribute("class", "edge horizontalEdge leftEdge fa"), u.appendChild(_);
	          }if (Number(v.substr(2, 1))) {
	            var E = document.createElement("i"),
	                L = document.createElement("i"),
	                A = u.querySelector(":scope > .title");E.setAttribute("class", "edge verticalEdge bottomEdge fa"), u.appendChild(E), L.setAttribute("class", "fa " + i.parentNodeSymbol + " symbol"), A.insertBefore(L, A.children[0]);
	          }
	        }u.addEventListener("mouseenter", n._hoverNode.bind(n)), u.addEventListener("mouseleave", n._hoverNode.bind(n)), u.addEventListener("click", n._dispatchClickEvent.bind(n)), i.draggable && (u.addEventListener("dragstart", n._onDragStart.bind(n)), u.addEventListener("dragover", n._onDragOver.bind(n)), u.addEventListener("dragend", n._onDragEnd.bind(n)), u.addEventListener("drop", n._onDrop.bind(n))), i.createNode && i.createNode(u, e), r(u);
	      });
	    } }, { key: "buildHierarchy", value: function value(e, t, n, i) {
	      var r = this,
	          s = this.options,
	          a = void 0,
	          o = t.children,
	          l = s.verticalDepth && n + 1 >= s.verticalDepth;Object.keys(t).length > 1 && (a = l ? e : document.createElement("table"), l || e.appendChild(a), this._createNode(t, n).then(function (e) {
	        if (l) a.insertBefore(e, a.firstChild);else {
	          var t = document.createElement("tr");t.innerHTML = "\n            <td " + (o ? 'colspan="' + 2 * o.length + '"' : "") + ">\n            </td>\n          ", t.children[0].appendChild(e), a.insertBefore(t, a.children[0] ? a.children[0] : null);
	        }i && i();
	      }).catch(function (e) {
	        console.error("Failed to creat node", e);
	      })), o && !function () {
	        1 === Object.keys(t).length && (a = e);var l = void 0,
	            d = s.verticalDepth && n + 2 >= s.verticalDepth,
	            c = r.chart.dataset.inEdit;if (l = c ? "addSiblings" === c ? "" : " hidden" : n + 1 >= s.depth ? " hidden" : "", !d) {
	          var h = document.createElement("tr");h.setAttribute("class", "lines" + l), h.innerHTML = '\n          <td colspan="' + 2 * o.length + '">\n            <div class="downLine"></div>\n          </td>\n        ', a.appendChild(h);
	        }var u = document.createElement("tr");u.setAttribute("class", "lines" + l), u.innerHTML = '\n        <td class="rightLine">&nbsp;</td>\n        ' + o.slice(1).map(function () {
	          return '\n          <td class="leftLine topLine">&nbsp;</td>\n          <td class="rightLine topLine">&nbsp;</td>\n          ';
	        }).join("") + '\n        <td class="leftLine">&nbsp;</td>\n      ';var f = void 0;if (d) {
	          if (f = document.createElement("ul"), l && f.classList.add(l.trim()), n + 2 === s.verticalDepth) {
	            var p = document.createElement("tr");p.setAttribute("class", "verticalNodes" + l), p.innerHTML = "<td></td>", p.firstChild.appendChild(f), a.appendChild(p);
	          } else a.appendChild(f);
	        } else f = document.createElement("tr"), f.setAttribute("class", "nodes" + l), a.appendChild(u), a.appendChild(f);o.forEach(function (e) {
	          var t = void 0;d ? t = document.createElement("li") : (t = document.createElement("td"), t.setAttribute("colspan", 2)), f.appendChild(t), r.buildHierarchy(t, e, n + 1, i);
	        });
	      }();
	    } }, { key: "_clickChart", value: function value(e) {
	      var t = this._closest(e.target, function (e) {
	        return e.classList && e.classList.contains("node");
	      });!t && this.chart.querySelector(".node.focused") && this.chart.querySelector(".node.focused").classList.remove("focused");
	    } }, { key: "_clickExportButton", value: function value() {
	      var e = this.options,
	          t = this.chartContainer,
	          n = t.querySelector(":scope > .mask"),
	          i = t.querySelector(".orgchart:not(.hidden)"),
	          r = "l2r" === e.direction || "r2l" === e.direction;n ? n.classList.remove("hidden") : (n = document.createElement("div"), n.setAttribute("class", "mask"), n.innerHTML = '<i class="fa fa-circle-o-notch fa-spin spinner"></i>', t.appendChild(n)), t.classList.add("canvasContainer"), window.html2canvas(i, { width: r ? i.clientHeight : i.clientWidth, height: r ? i.clientWidth : i.clientHeight, onclone: function onclone(e) {
	          var t = e.querySelector(".canvasContainer");t.style.overflow = "visible", t.querySelector(".orgchart:not(.hidden)").transform = "";
	        } }).then(function (e) {
	        var n = t.querySelector(".oc-download-btn");t.querySelector(".mask").classList.add("hidden"), n.setAttribute("href", e.toDataURL()), n.click();
	      }).catch(function (e) {
	        console.error("Failed to export the curent orgchart!", e);
	      }).finally(function () {
	        t.classList.remove("canvasContainer");
	      });
	    } }, { key: "_loopChart", value: function value(e) {
	      var t = this,
	          n = { id: e.querySelector(".node").id };return e.children[3] && Array.from(e.children[3].children).forEach(function (e) {
	        n.children || (n.children = []), n.children.push(t._loopChart(e.firstChild));
	      }), n;
	    } }, { key: "getHierarchy", value: function value() {
	      return this.chart.querySelector(".node").id ? this._loopChart(this.chart.querySelector("table")) : "Error: Nodes of orghcart to be exported must have id attribute!";
	    } }, { key: "_onPanStart", value: function value(e) {
	      var t = e.currentTarget;if (this._closest(e.target, function (e) {
	        return e.classList && e.classList.contains("node");
	      }) || e.touches && e.touches.length > 1) return void (t.dataset.panning = !1);t.style.cursor = "move", t.dataset.panning = !0;var n = 0,
	          i = 0,
	          r = window.getComputedStyle(t).transform;if ("none" !== r) {
	        var s = r.split(",");r.includes("3d") ? (n = Number.parseInt(s[12], 10), i = Number.parseInt(s[13], 10)) : (n = Number.parseInt(s[4], 10), i = Number.parseInt(s[5], 10));
	      }var a = 0,
	          o = 0;if (e.targetTouches) {
	        if (1 === e.targetTouches.length) a = e.targetTouches[0].pageX - n, o = e.targetTouches[0].pageY - i;else if (e.targetTouches.length > 1) return;
	      } else a = e.pageX - n, o = e.pageY - i;t.dataset.panStart = JSON.stringify({ startX: a, startY: o }), t.addEventListener("mousemove", this._onPanning.bind(this)), t.addEventListener("touchmove", this._onPanning.bind(this));
	    } }, { key: "_onPanning", value: function value(e) {
	      var t = e.currentTarget;if ("false" !== t.dataset.panning) {
	        var n = 0,
	            i = 0,
	            r = JSON.parse(t.dataset.panStart),
	            s = r.startX,
	            a = r.startY;if (e.targetTouches) {
	          if (1 === e.targetTouches.length) n = e.targetTouches[0].pageX - s, i = e.targetTouches[0].pageY - a;else if (e.targetTouches.length > 1) return;
	        } else n = e.pageX - s, i = e.pageY - a;var o = window.getComputedStyle(t).transform;if ("none" === o) o.includes("3d") ? t.style.transform = "matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, " + n + ", " + i + ", 0, 1)" : t.style.transform = "matrix(1, 0, 0, 1, " + n + ", " + i + ")";else {
	          var l = o.split(",");o.includes("3d") ? (l[12] = n, l[13] = i) : (l[4] = n, l[5] = i + ")"), t.style.transform = l.join(",");
	        }
	      }
	    } }, { key: "_onPanEnd", value: function value(e) {
	      var t = this.chart;"true" === t.dataset.panning && (t.dataset.panning = !1, t.style.cursor = "default", document.body.removeEventListener("mousemove", this._onPanning), document.body.removeEventListener("touchmove", this._onPanning));
	    } }, { key: "_setChartScale", value: function value(e, t) {
	      var n = window.getComputedStyle(e).transform;if ("none" === n) e.style.transform = "scale(" + t + "," + t + ")";else {
	        var i = n.split(",");n.includes("3d") ? e.style.transform = n + " scale3d(" + t + "," + t + ", 1)" : (i[0] = "matrix(" + t, i[3] = t, e.style.transform = n + " scale(" + t + "," + t + ")");
	      }e.dataset.scale = t;
	    } }, { key: "_onWheeling", value: function value(e) {
	      e.preventDefault();var t = e.deltaY > 0 ? .8 : 1.2;this._setChartScale(this.chart, t);
	    } }, { key: "_getPinchDist", value: function value(e) {
	      return Math.sqrt((e.touches[0].clientX - e.touches[1].clientX) * (e.touches[0].clientX - e.touches[1].clientX) + (e.touches[0].clientY - e.touches[1].clientY) * (e.touches[0].clientY - e.touches[1].clientY));
	    } }, { key: "_onTouchStart", value: function value(e) {
	      var t = this.chart;if (e.touches && 2 === e.touches.length) {
	        var n = this._getPinchDist(e);t.dataset.pinching = !0, t.dataset.pinchDistStart = n;
	      }
	    } }, { key: "_onTouchMove", value: function value(e) {
	      var t = this.chart;if (t.dataset.pinching) {
	        var n = this._getPinchDist(e);t.dataset.pinchDistEnd = n;
	      }
	    } }, { key: "_onTouchEnd", value: function value(e) {
	      var t = this.chart;if (t.dataset.pinching) {
	        t.dataset.pinching = !1;var n = t.dataset.pinchDistEnd - t.dataset.pinchDistStart;n > 0 ? this._setChartScale(t, 1) : n < 0 && this._setChartScale(t, -1);
	      }
	    } }, { key: "name", get: function get() {
	      return this._name;
	    } }]), e;
	}();exports.default = OrgChart;

/***/ }
/******/ ]);
//# sourceMappingURL=bundle.js.map