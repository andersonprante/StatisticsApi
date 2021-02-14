KB.component('chart-s-curve', function (containerElement, options) {
  this.render = function () {
    let { payload } = options
    KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());
    c3.generate({
      data: {
        x: 'Date',
        rows: payload
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