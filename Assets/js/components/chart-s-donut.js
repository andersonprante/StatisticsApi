KB.component('chart-s-donut', function (containerElement, options) {
  this.render = function () {
    let { payload, id } = options
    const today = new Date().toISOString().slice(0, 10)
    chart_id = `chart-s-donut-${id}`

    KB.dom(containerElement).add(KB.dom('div').attr('id', chart_id).build());

    c3.generate({
      bindto: d3.select(`#${chart_id}`),
      data: {
        columns: payload,
        type : 'donut',
        onclick: function (d, i) { console.log("onclick", d, i); },
        onmouseover: function (d, i) { console.log("onmouseover", d, i); },
        onmouseout: function (d, i) { console.log("onmouseout", d, i); }
      },
      donut: {
        title: "Tarefas"
      }
    });
  }
}) 