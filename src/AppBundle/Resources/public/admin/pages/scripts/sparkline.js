jQuery(function($) {
    var values = $('#sparkline_bar5').data('values');
    console.log(values);
    $("#sparkline_bar5").sparkline(values, {
        type: 'bar',
        width: '100',
        tooltipFormat: '{{value:levels}} - {{value}}',
        tooltipValueLookups: {
            levels: values
        },
        barWidth: 5,
        height: '55',
        barColor: '#35aa47',
        negBarColor: '#e02222'
    });
});