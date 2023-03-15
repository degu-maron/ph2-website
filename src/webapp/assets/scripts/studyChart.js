'use strict'
{
  let type = 'doughnut';
  const ctx = document.getElementById('studyChart').getContext('2d');

  const data = {
    labels: ["N予備校", "ドットインストール", "課題"],
    datasets: [{
      data: [40, 20, 40],
      backgroundColor: ['LightSkyBlue', 'SkyBlue', 'CornflowerBlue'],
      borderWidth: 0,
    }],
  };

  const options = {
    plugins: {
      title: {
        display: true,
        text: '学習コンテンツ',
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

  const studyChart = new Chart(ctx, {
    type: type,
    data: data,
    options: options,
  })

  
}