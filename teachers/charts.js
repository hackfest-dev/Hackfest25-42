
// Fallback chart rendering function
function renderFallbackCharts(analyticsData) {
  console.log('Rendering fallback charts');
  
  // Performance chart
  var performanceCtx = document.getElementById('performanceChart');
  if (performanceCtx && analyticsData && analyticsData.questions) {
    var labels = [];
    var data = [];
    
    for (var i = 0; i < analyticsData.questions.length; i++) {
      labels.push('Q' + analyticsData.questions[i].question_id);
      data.push(analyticsData.questions[i].correct_percentage);
    }
    
    new Chart(performanceCtx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Correct Answers (%)',
          data: data,
          backgroundColor: '#0A2558',
          borderColor: '#0A2558',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            title: {
              display: true,
              text: 'Percentage of Correct Answers'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Questions'
            }
          }
        }
      }
    });
  }
  
  // Difficulty chart
  var difficultyCtx = document.getElementById('difficultyChart');
  if (difficultyCtx && analyticsData && analyticsData.questions) {
    var easy = 0;
    var medium = 0;
    var hard = 0;
    
    for (var i = 0; i < analyticsData.questions.length; i++) {
      var percentage = analyticsData.questions[i].correct_percentage;
      if (percentage >= 70) {
        easy++;
      } else if (percentage >= 40) {
        medium++;
      } else {
        hard++;
      }
    }
    
    new Chart(difficultyCtx.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Easy (>70%)', 'Medium (40-70%)', 'Hard (<40%)'],
        datasets: [{
          data: [easy, medium, hard],
          backgroundColor: ['#42A5F5', '#FFA726', '#EF5350'],
          hoverOffset: 4
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }
}
