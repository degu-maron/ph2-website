'use strict'
{
  (async function () {
    const url1 = '/webapp/hours.json';
    const url2 = '/webapp/days.json';

    async function hoursGraph() {
      const response1 = await fetch(url1);
      const hours = await response1.json();
      const response2 = await fetch(url2);
      const days = await response2.json();

      let type = 'bar';

      const ctx = document.getElementById('barGraph').getContext('2d');
      const gradientStroke = ctx.createLinearGradient(0, 0, 0, 100);
      gradientStroke.addColorStop(0, 'rgb(20, 191, 250)');
      gradientStroke.addColorStop(0.5, 'rgb(20, 180, 240)')
      gradientStroke.addColorStop(1, 'rgb(19, 131, 201)');
      document.getElementsByClassName

      const data = {
        // labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31],
        labels: days,
        datasets: [{
          label: '',
          // data: [2.5, 4.3, 1.0, 3.3, 3.3, 4.0, 6.2, 7.3, 1.5, 4.0, 2.0, 5.5, 7.0, 8.0, 5.5, 3.7, 4.3, 0.7, 0.7, 1.0, 4.0, 2.5, 5.5, 1.5, 6.2, 8.0, 8.0, 2.5, 0.7, 4.0, 1.3],
          data: hours,
          backgroundColor: gradientStroke,
          borderRadius: 50,
          barThickness: 4,
        }]
      };

      const options = {
        scales: {
          x: {
            grid: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              autoSkip: false,
              maxRotation: 0,  // 数字が傾かないように
              minRotation: 0,
              color: 'rgb(50, 101, 255)',
              callback: (value) => {
                if (value % 2 == 0) {
                  return '';
                } else {
                  return value + 1;
                };
              },
            },
          },
          y: {
            ticks: {
              color: 'rgb(50, 101, 255)',
              suggestedMin: 0,
              suggestedMax: 8,
              stepSize: 2,
              callback: (value) => value + 'h'
            },
            grid: {
              display: false,
              drawBorder: false,
            }
          },
        },
        animation: false,
        plugins: {
          legend: {
            display: false
          },
        },
      };
      const myChart = new Chart(ctx, {
        type: type,
        data: data,
        options: options,
      });
      myChart;
    };
    await hoursGraph();
  })();
}
  