'use strict'
{
  let type = 'doughnut';
  const ctx = document.getElementById('languageChart').getContext('2d');

  const data = {
    labels: ['HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel', 'SQL', 'SHELL', '情報システム基礎知識(その他)'],
    datasets: [{
      data: [30, 20, 10, 5, 5, 20, 20, 10],
      backgroundColor: ['LightSkyBlue', 'SkyBlue', 'CornflowerBlue','RoyalBlue',  'Blue', 'MediumBlue', 'SlateBlue', 'BlueViolet'],
      borderWidth: 0,
    }],

  };

  const options = {
    plugins: {
      title: {
        display: true,
        text: '学習言語',
        align: 'start',
        font: {
          size: 15,
        },
      },
      legend: {
        display: false,
      },
    },
    pieHole: 0.2,
    animation: false,
    maintainAspectRatio: false,
  };

  const languageChart = new Chart(ctx, {
    type: type,
    data: data,
    options: options,
  })

}