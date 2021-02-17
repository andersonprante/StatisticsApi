KB.component('chart-s-gauge', function (containerElement, options) {
  this.render = function () {
    let { payload, id } = options
    const today = new Date().toISOString().slice(0, 10)
    chart_id = `chart-s-gauge-${id}`

    KB.dom(containerElement).add(KB.dom('div').attr('id', chart_id).build());

    c3.generate({
      bindto: d3.select(`#${chart_id}`),
      data: {
        columns: payload,
        type: 'gauge',
        onclick: function (d, i) { console.log("onclick", d, i); },
        onmouseover: function (d, i) { console.log("onmouseover", d, i); },
        onmouseout: function (d, i) { console.log("onmouseout", d, i); }
      },
      gauge: {
       label: {
           format: function(value, ratio) {
               return value * 100 + ' %';
           },
           show: true
       },
      min: -1,
      max: 1, 
      width: 25
    },
    color: {
        pattern: ['#FF0000', '#F6C600', '#60B044', '#F6C600', '#FF0000'],
        threshold: {
          unit: 'value',
          values: [-.3, -.2, .2, .3, 1]
        }
    },
    size: {
        height: 180
    }
    });
  }
}) 