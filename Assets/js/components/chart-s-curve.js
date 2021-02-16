KB.component('chart-s-curve', function (containerElement, options) {
  this.render = function () {
    let { payload, id } = options
    const today = new Date().toISOString().slice(0, 10)
    chart_id = `chart-${id}`

    KB.dom(containerElement).add(KB.dom('div').attr('id', chart_id).build());

    c3.generate({
      bindto: d3.select(`#${chart_id}`),
      data: {
        x: 'Date',
        rows: payload,
      },
      point: {
        show: false
      },
      grid: {
        x: {
            lines: [
                {value: today, text: today},
            ]
        }
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