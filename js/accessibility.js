jQuery(document).ready(function() {
	if(typeof jQuery.fn.bt!="undefined") {
		jQuery('.error-hover').bt({ajaxPath: ["jQuery(this).attr('href')", '#test-overview' ],
								   positions: ['right', 'left'],
								   fill: '#F4F4F4',
								   strokeStyle: '#666666', 
								   spikeLength: 20,
								   spikeGirth: 10,
								   width: 350,
								   overlap: 0,
								   centerPointY: 1,
								   cornerRadius: 0, 
								   shadow: true,
								   shadowColor: 'rgba(0,0,0,.5)',
								   shadowBlur: 8,
								   shadowOffsetX: 4,
								   shadowOffsetY: 4});
	}
});