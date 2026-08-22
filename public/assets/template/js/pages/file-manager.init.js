/*
Template Name: Nomzie - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: file maneger init js
*/



// get colors array from the string
function getChartColorsArray(chartId) {
  if (document.getElementById(chartId) !== null) {
      var colors = document.getElementById(chartId).getAttribute("data-colors");
      if (colors) {
          colors = JSON.parse(colors);
          return colors.map(function (value) {
              var newValue = value.replace(" ", "");
              if (newValue.indexOf(",") === -1) {
                  var color = getComputedStyle(document.documentElement).getPropertyValue(
                      newValue
                  );
                  if (color) return color;
                  else return newValue;
              } else {
                  var val = value.split(",");
                  if (val.length == 2) {
                      var rgbaColor = getComputedStyle(
                          document.documentElement
                      ).getPropertyValue(val[0]);
                      rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                      return rgbaColor;
                  } else {
                      return newValue;
                  }
              }
          });
      } else {
          console.warn('data-colors atributes not found on', chartId);
      }
  }
}


var chartmini1Colors = getChartColorsArray("stroked_radialbar");
if (chartmini1Colors) {
var options = {
  series: [76.20],
  chart: {
  height: 270,
  type: 'radialBar',
  offsetY: -20
},
plotOptions: {
  radialBar: {
    startAngle: -135,
    endAngle: 135,
    dataLabels: {
      name: {
        fontSize: '16px',
        color: undefined,
        offsetY: 120
      },
      value: {
        offsetY: 76,
        fontSize: '20px',
        color: undefined,
        formatter: function (val) {
          return val + "%";
        }
      }
    }
  }
},
fill: {
  type: 'gradient',
  gradient: {
      shade: 'dark',
      shadeIntensity: 0.15,
      inverseColors: false,
      opacityFrom: 1,
      opacityTo: 1,
      stops: [0, 50, 65, 91]
  },
},
stroke: {
  dashArray: 4
},
labels: ['64 GB used'],
colors: chartmini1Colors,
};

var chart = new ApexCharts(document.querySelector("#stroked_radialbar"), options);
chart.render();
}