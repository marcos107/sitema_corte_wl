/*
Template Name: Nomzie - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Ecommerce Dashboard init js
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


// mini-1
var chartmini1Colors = getChartColorsArray("mini-1");
if (chartmini1Colors) {
var options = {
    series: [{
      data: [2, 36, 22, 30, 12, 38]
    }],
    chart: {
      type: 'area',
      height: 40,
      sparkline: {
        enabled: true
      }  
    },
    colors: chartmini1Colors,
    stroke: {
    curve: 'straight',
    width: 1.7,
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.35,
            opacityTo: 0.05,
            stops: [20, 100, 100, 100]
          },
    },
    tooltip: {
      fixed: {
        enabled: false
      },
      x: {
        show: false
      },
      y: {
        title: {
          formatter: function (seriesName) {
            return ''
          }
        }
      },
      marker: {
        show: false
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#mini-1"), options);
chart.render();

}


// mini-2
var chartmini1Colors = getChartColorsArray("mini-2");
if (chartmini1Colors) {
var options = {
    series: [{
      data: [36, 12, 30, 20, 36, 14]
    }],
    chart: {
      type: 'area',
      height: 40,
      sparkline: {
        enabled: true
      }  
    },
    colors: chartmini1Colors,
    stroke: {
    curve: 'straight',
    width: 1.7,
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.35,
            opacityTo: 0.05,
            stops: [20, 100, 100, 100]
          },
    },
    tooltip: {
      fixed: {
        enabled: false
      },
      x: {
        show: false
      },
      y: {
        title: {
          formatter: function (seriesName) {
            return ''
          }
        }
      },
      marker: {
        show: false
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#mini-2"), options);
chart.render();

}

// mini-3
var chartmini1Colors = getChartColorsArray("mini-3");
if (chartmini1Colors) {
var options = {
    series: [{
      data: [14, 40, 14, 46, 28, 38]
    }],
    chart: {
      type: 'area',
      height: 40,
      sparkline: {
        enabled: true
      }  
    },
    colors: chartmini1Colors,
    stroke: {
    curve: 'straight',
    width: 1.7,
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.35,
            opacityTo: 0.05,
            stops: [20, 100, 100, 100]
          },
    },
    tooltip: {
      fixed: {
        enabled: false
      },
      x: {
        show: false
      },
      y: {
        title: {
          formatter: function (seriesName) {
            return ''
          }
        }
      },
      marker: {
        show: false
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#mini-3"), options);
chart.render();

}

// mini-4
var chartmini1Colors = getChartColorsArray("mini-4");
if (chartmini1Colors) {
var options = {
    series: [{
      data: [34, 2, 30, 12, 35, 20]
    }],
    chart: {
      type: 'area',
      height: 40,
      sparkline: {
        enabled: true
      }  
    },
    colors: chartmini1Colors,
    stroke: {
    curve: 'straight',
    width: 1.7,
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.35,
            opacityTo: 0.05,
            stops: [20, 100, 100, 100]
          },
    },
    tooltip: {
      fixed: {
        enabled: false
      },
      x: {
        show: false
      },
      y: {
        title: {
          formatter: function (seriesName) {
            return ''
          }
        }
      },
      marker: {
        show: false
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#mini-4"), options);
chart.render();

}

// Total Portfolio Donut Charts
var donutchartportfolioColors = getChartColorsArray("portfolio_donut_charts");
if (donutchartportfolioColors) {
  var options = {
    series: [9564, 2221, 2046, 1556],
    labels: ["Watch", "Iphone", "Book", "laptop"],
    chart: {
      type: "donut",
      height: 268,
    },

    plotOptions: {
      pie: {
        size: 100,
        offsetX: 0,
        offsetY: 0,
        donut: {
          size: "72%",
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: "30px",
              offsetY: -12,
            },
            value: {
              show: true,
              fontSize: "25px",
              color: "#343a40",
              fontWeight: 500,
              offsetY: 5,
            },
            total: {
              show: true,
              fontSize: "16px",
              label: "Total value",
              color: "#9599ad",
              fontWeight: 500,
              
            },
          },
        },
      },
    },
    dataLabels: {
      enabled: false,
    },
    legend: {
      show: false,
    },

    stroke: {
      lineCap: "round",
      width: 2,
    },
    colors: donutchartportfolioColors,
  };
  var chart = new ApexCharts(document.querySelector("#portfolio_donut_charts"), options);
  chart.render();
}

// Sale
var SalesColors = getChartColorsArray("sales-statistics");
if (SalesColors) {
var options = {
  series: [
  {
    name: 'Actual',

    data: [
      {
        x: '2011',
        y: 2000,
        goals: [
          {
            name: 'Expected',
            value: 2400,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2012',
        y: 5000,
        goals: [
          {
            name: 'Expected',
            value: 5400,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2013',
        y: 4800,
        goals: [
          {
            name: 'Expected',
            value: 5200,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2014',
        y: 6100,
        goals: [
          {
            name: 'Expected',
            value: 6500,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2015',
        y: 6200,
        goals: [
          {
            name: 'Expected',
            value: 6600,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2016',
        y: 7100,
        goals: [
          {
            name: 'Expected',
            value: 7500,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2017',
        y: 8300,
        goals: [
          {
            name: 'Expected',
            value: 8700,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2018',
        y: 6900,
        goals: [
          {
            name: 'Expected',
            value: 7300,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      
      {
        x: '2019',
        y: 6200,
        goals: [
          {
            name: 'Expected',
            value: 6600,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2020',
        y: 7100,
        goals: [
          {
            name: 'Expected',
            value: 7500,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2021',
        y: 5300,
        goals: [
          {
            name: 'Expected',
            value: 5700,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      
      {
        x: '2022',
        y: 4100,
        goals: [
          {
            name: 'Expected',
            value: 4500,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
      {
        x: '2023',
        y: 5300,
        goals: [
          {
            name: 'Expected',
            value: 5700,
            strokeHeight: 4,
            strokeColor: SalesColors[1],
          }
        ]
      },
    ]
  }
],

  chart: {
  height: 380,
  type: 'bar',
  toolbar: {
    show: false
  },
},
plotOptions: {
  bar: {
    columnWidth: '30%'
  }
},

legend: {
  show: false,
},

colors: SalesColors[0],
dataLabels: {
  enabled: false
},

};

var chart = new ApexCharts(document.querySelector("#sales-statistics"), options);
chart.render();
}



// Stacked Area Charts
var barchartColors = getChartColorsArray("sales-report");
var options1 = {
  chart: {
    type: 'area',
    height: 380,
    toolbar: {
      show: false
    },
  },
  series: [{
    name: 'Incomes',
    data: [0, 20, 35, 45, 35, 55, 65, 50, 65, 75, 60, 75]
  }, {
    name: 'Expenses',
    data: [15, 09, 17, 32, 25, 68, 80, 68, 84, 94, 74, 62]
  }
  ],
  stroke: {
    curve: 'straight',
    width: ['3','3'],
  },
  grid:{
    xaxis: {
      lines: {
          show: true
      }
  },   
  yaxis: {
      lines: {
          show: true
      }
  }, 
  },
  colors: barchartColors,
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Now','Des'],
  },
  legend: {
      show: false,
  },

  fill: {
      type: 'gradient',
      gradient: {
          shadeIntensity: 1,
          inverseColors: false,
          opacityFrom: 0.35,
          opacityTo: 0.1,
          stops: [30, 100, 100, 100]
        },
    },
    dataLabels: {
      enabled: false
    },
  tooltip: {
    fixed: {
      enabled: false
    }
    ,
    x: {
      show: false
    }
    ,
    y: {
      title: {
        formatter: function (seriesName) {
          return ''
        }
      }
    }
    ,
    marker: {
      show: false
    }
  }
}
new ApexCharts(document.querySelector("#sales-report"), options1).render();



//
// Sales Analytics Chart
var barchartColors = getChartColorsArray("sales-analytics-chart");
var options = {
  chart: {
      height: 380,
      type: 'line',
      stacked: false,
      toolbar: {
          show: false
      }
  },
  stroke: {
      width: [0, 2, 3],
      curve: 'smooth',
      dashArray: [4, 0,4], 
  },
  plotOptions: {
      bar: {
          columnWidth: '22%'
      }
  },
  colors: barchartColors,
  series: [{
    name: 'Incomes',
      type: 'bar',
      data: [36, 31, 27, 37, 23, 42, 37, 41, 34, 42, 34]
  }, {
    name: 'Earnings',
      type: 'area',
      data: [24, 45, 25, 52, 30, 43, 21, 41, 51, 27, 43]
  }, {
    name: 'Refunds',
      type: 'line',
      data: [30, 50, 33, 57, 35, 49, 27, 46, 56, 36, 50]
  }],
  fill: {
      opacity: [0.85, 0.20, 1],
      gradient: {
          inverseColors: false,
          shade: 'light',
          type: "vertical",
          opacityFrom: 0.85,
          opacityTo: 0.0,
          stops: [0, 100, 100, 100]
      }
  },
  legend: {
    show: false,
},
  labels: ['01/01/2003', '02/01/2003', '03/01/2003', 
  '04/01/2003', '05/01/2003', '06/01/2003', '07/01/2003',
   '08/01/2003', '09/01/2003', '10/01/2003', '11/01/2003'],
  markers: {
      size: 4
  },

  xaxis: {
      type: 'datetime'
  },
  tooltip: {
      shared: true,
      intersect: false,
      y: {
          formatter: function (y) {
              if (typeof y !== "undefined") {
                  return y.toFixed(0) + " points";
              }
              return y;

          }
      }
  },
  grid: {
      borderColor: '#f1f1f1'
  }
}

var chart = new ApexCharts(
  document.querySelector("#sales-analytics-chart"),
  options
);

chart.render();

//  Column with Rotated Labels
var chartColumnRotateLabelsColors = getChartColorsArray("column_rotated_labels");
if (chartColumnRotateLabelsColors) {
    var options = {
      series: [{
        name:'Aetworks',
        type: 'bar',
        data: [44, 75, 62, 75, 62, 63, 41, 53, 65, 51, 70, 65, 35]
    }, {
      name: 'Auction',
      type: 'line',
        data: [23, 52, 40, 59, 33, 44, 25, 32, 41, 36, 56, 43, 25]
    }],

        chart: {
            height: 395,
            type: 'bar',
            toolbar: {
                show: false,
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                columnWidth: '27%',
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
          width: [2, 4]
        },

        markers: {
          size: 4,
        },
       
        colors: chartColumnRotateLabelsColors,
        xaxis: {
            labels: {
                rotate: -45
            },
            categories: ['2011', '2012', '2013', '2014', '2015', '2016',
                '2017', '2018', '2019', '2020', '2021', '2022', '2023'
            ],
            tickPlacement: 'on'
        },

        legend: {
          show: false
      },
      dataLabels: {
        enabled: false,
    },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "horizontal",
                shadeIntensity: 0.15,
                gradientToColors: undefined,
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 0.85,
                stops: [50, 0, 100]
            },
        }
    };

    var chart = new ApexCharts(document.querySelector("#column_rotated_labels"), options);
    chart.render();
}

// Basic Treemaps
var barchartColors = getChartColorsArray("worldwide_sales");
var options = {
    series: [
    {
      data: [
        {
          x: 'Greenland',
          y: 218
        },
        {
          x: 'Canada',
          y: 149
        },
        {
          x: 'Brazil',
          y: 184
        },
        {
          x: 'Egypt',
          y: 55
        },
        {
          x: 'Russia',
          y: 84
        },
        {
          x: 'China',
          y: 31
        },
        {
          x: 'United States',
          y: 70
        },
        {
          x: 'Norway',
          y: 30
        },
        {
          x: 'Ukraine',
          y: 44
        },
        {
          x: 'Hyderabad',
          y: 68
        },
        {
          x: 'Lucknow',
          y: 28
        },
        {
          x: 'Indore',
          y: 19
        },
        {
          x: 'Kanpur',
          y: 29
        }
      ]
    }
  ],
    legend: {
    show: false
  },
  fill: {
    opacity: 0.9,
  },
  chart: {
    height: 210,
    type: 'treemap',
    toolbar: {
      show: false
    }
  },
  colors: barchartColors,

  };

  var chart = new ApexCharts(document.querySelector("#worldwide_sales"), options);
  chart.render();


// radialBar
var barchartColors = getChartColorsArray("social-source");
var options = {
  labels: ["E-Commerce", "Facebook", "Instagram"],
  series: [38, 24, 16],
  chart: {
      height: 370,
  type: 'donut',
},
plotOptions: {
  pie: {
    startAngle: -90,
    endAngle: 90,
    offsetY: 10,
    donut: {
      size: '77%',
    },
  }
},
colors: barchartColors,
grid: {
  padding: {
    bottom: -190
  }
},

legend: {
  show: false,
},

responsive: [{
  breakpoint: 320,
  options: {
    chart: {
      width: 180
    },
    legend: {
      position: 'bottom'
    }
  }
}]
};

var chart = new ApexCharts(document.querySelector("#social-source"), options);
chart.render();


//  Radialbar Charts Expense
var chartRadialbarBasicColors = getChartColorsArray("basic_expense");
if(chartRadialbarBasicColors){
var options = {
    series: [70],
    chart: {
        height: 253,
        type: 'radialBar',
    },
    plotOptions: {
        radialBar: {
            hollow: {
                size: '64%',
            }
        },
    },
    labels: ['Expense'],
    colors: chartRadialbarBasicColors
};

var chart = new ApexCharts(document.querySelector("#basic_expense"), options);
chart.render();
}


//  Radialbar Charts Order
var chartRadialbarBasicColors = getChartColorsArray("basic_order");
if(chartRadialbarBasicColors){
var options = {
    series: [80],
    chart: {
        height: 253,
        type: 'radialBar',
    },
    plotOptions: {
        radialBar: {
            hollow: {
                size: '64%',
            }
        },
    },
    labels: ['Order'],
    colors: chartRadialbarBasicColors
};

var chart = new ApexCharts(document.querySelector("#basic_order"), options);
chart.render();
}


// sparkline-1
var barchartColors = getChartColorsArray("chart-sparkline1");
var sparklineoptions1 = {
  series: [{
    data: [25, 66, 41, 89, 36, 9, 54 ,84]
  }],
  chart: {
    type: 'area',
    width: 120,
    height: 40,
    sparkline: {
      enabled: true
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100]
    },
  },
  stroke: {
    curve: 'smooth',
    width: 2,
  },
  colors: barchartColors,
  tooltip: {
    fixed: {
      enabled: false
    },
    x: {
      show: false
    },
    y: {
      title: {
        formatter: function (seriesName) {
          return ''
        }
      }
    },
    marker: {
      show: false
    }
  }
};

var sparklinechart1 = new ApexCharts(document.querySelector("#chart-sparkline1"), sparklineoptions1);
sparklinechart1.render();


// sparkline-2
var barchartColors = getChartColorsArray("chart-sparkline2");
var sparklineoptions1 = {
  series: [{
    data: [50, 15, 35, 62, 36, 9, 54,23]
  }],
  chart: {
    type: 'area',
    width: 120,
    height: 40,
    sparkline: {
      enabled: true
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100]
    },
  },
  stroke: {
    curve: 'smooth',
    width: 2,
  },
  colors: barchartColors,
  tooltip: {
    fixed: {
      enabled: false
    },
    x: {
      show: false
    },
    y: {
      title: {
        formatter: function (seriesName) {
          return ''
        }
      }
    },
    marker: {
      show: false
    }
  }
};

var sparklinechart1 = new ApexCharts(document.querySelector("#chart-sparkline2"), sparklineoptions1);
sparklinechart1.render();


// sparkline-3
var barchartColors = getChartColorsArray("chart-sparkline3");
var sparklineoptions1 = {
  series: [{
    data: [25, 35, 35, 89, 36, 9, 54, 25]
  }],
  chart: {
    type: 'area',
    width: 120,
    height: 40,
    sparkline: {
      enabled: true
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100]
    },
  },
  stroke: {
    curve: 'smooth',
    width: 2,
  },
  colors: barchartColors,
  tooltip: {
    fixed: {
      enabled: false
    },
    x: {
      show: false
    },
    y: {
      title: {
        formatter: function (seriesName) {
          return ''
        }
      }
    },
    marker: {
      show: false
    }
  }
};

var sparklinechart1 = new ApexCharts(document.querySelector("#chart-sparkline3"), sparklineoptions1);
sparklinechart1.render();


// sparkline-4
var barchartColors = getChartColorsArray("chart-sparkline4");
var sparklineoptions1 = {
  series: [{
    data: [50, 15, 35, 34, 36, 41, 32, 25]
  }],
  chart: {
    type: 'area',
    width: 120,
    height: 40,
    sparkline: {
      enabled: true
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100]
    },
  },
  stroke: {
    curve: 'smooth',
    width: 2,
  },
  colors: barchartColors,
  tooltip: {
    fixed: {
      enabled: false
    },
    x: {
      show: false
    },
    y: {
      title: {
        formatter: function (seriesName) {
          return ''
        }
      }
    },
    marker: {
      show: false
    }
  }
};

var sparklinechart1 = new ApexCharts(document.querySelector("#chart-sparkline4"), sparklineoptions1);
sparklinechart1.render();


// sparkline-5
var barchartColors = getChartColorsArray("chart-sparkline5");
var sparklineoptions1 = {
  series: [{
    data: [45, 53, 24, 89, 36, 32, 54, 63]
  }],
  chart: {
    type: 'area',
    width: 120,
    height: 40,
    sparkline: {
      enabled: true
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      inverseColors: false,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100]
    },
  },
  stroke: {
    curve: 'smooth',
    width: 2,
  },
  colors: barchartColors,
  tooltip: {
    fixed: {
      enabled: false
    },
    x: {
      show: false
    },
    y: {
      title: {
        formatter: function (seriesName) {
          return ''
        }
      }
    },
    marker: {
      show: false
    }
  }
};

var sparklinechart1 = new ApexCharts(document.querySelector("#chart-sparkline5"), sparklineoptions1);
sparklinechart1.render();


// Vertical Swiper
var swiper = new Swiper(".vertical-swiper", {
  loop: true,
  direction: "vertical",
  spaceBetween: 20,
  autoplay: {
      delay: 2500,
      disableOnInteraction: false,
  },
  pagination: {
      el: ".swiper-pagination",
      clickable: true,
  },
});