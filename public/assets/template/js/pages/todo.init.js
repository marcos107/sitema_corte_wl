/*
Template Name: Nomzie - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Todo init js
*/


// get colors array from the string
function getChartColorsArray(chartId) {
  if (document.getElementById(chartId) !== null) {
      var colors = document.getElementById(chartId).getAttribute("data-colors");
      if (colors) {
          colors = JSON.parse(colors);
          return colors.map(function(value) {
              var newValue = value.replace(" ", "");
              if (newValue.indexOf(",") === -1) {
                  var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                  if (color) return color;
                  else return newValue;;
              } else {
                  var val = value.split(',');
                  if (val.length == 2) {
                      var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
                      rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                      return rgbaColor;
                  } else {
                      return newValue;
                  }
              }
          });
      }
  }
}


//  Active 
var chartUserColors = getChartColorsArray("active-users");
if (chartUserColors) {
var options = {
    series: [{
      name: "Users",
        data: [11, 17, 15, 20, 18, 23, 17,14, 12, 19]
    }],
    chart: {
        toolbar: {
            show: false,
        },
        offsetX: -5,
        offsetY: -5,
        height: 160,
        type: 'bar',
        events: {
            click: function (chart, w, e) {
            }
        }
    },
    plotOptions: {
        bar: {
            columnWidth: '60%',
            distributed: true,
        }
    },
    fill: {
      opacity: 1
  },
    dataLabels: {
        enabled: false
    },
    legend: {
        show: false
    },
    axisTicks: {
      show: false
    },
    tooltip: {
      enabled: true,
    },
    
    xaxis: {
      labels: {
        show: false
      },
        axisBorder: {
          show: false,
      },
      axisTicks: {
          show: false,
      },
    },
    axisBorder: {
      show: false
    },
    yaxis: {
      show: false,
    },
    colors: chartUserColors,
};

var chart = new ApexCharts(document.querySelector("#active-users"), options);
chart.render();
}

// flatpickr

flatpickr('#CreateTask-due-date');

