KB.component('chart-s-curve', function (containerElement, options) {
  this.render = function () {
    let { payload, id } = options
    chart_id = `chart-${id}`
    console.log(chart_id);
    console.log(payload);
    KB.dom(containerElement).add(KB.dom('div').attr('id', chart_id).build());
    c3.generate({
      bindto: d3.select(`#${chart_id}`),
      data: {
        x: 'Date',
        rows: payload,
        type: 'spline'
      },
      line: {
        connectNull: true,
      },
      axis: {
        x: {
          type: 'timeseries',
          tick: {
            format: '%Y-%m-%d'
          }
        }
      }
    });
  }
}) 