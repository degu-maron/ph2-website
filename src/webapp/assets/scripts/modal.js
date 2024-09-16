'use strict'

{
  const openButton = document.getElementById('js-recordButton')
  const modal = document.getElementById('js-modal')
  const modalContainer = document.getElementById('js-modalContainer')
  const closeButton = document.getElementById('js-closeButton')
  const closeButton2 = document.getElementById('js-closeButton2')
  const closeButton3 = document.getElementById('js-closeButton3')
  const inputDay = document.getElementById('day')
  const twitterInput = document.getElementById('twitter')
  const shareButton = document.getElementById('share_button')
  const calendar = document.getElementById('js-calendar')
  const modalRecord = document.getElementById('modalRecordButton')
  const backButton = document.getElementById('js-backButton')
  const loading = document.getElementById('js-loading')
  const done = document.getElementById('js-done')

  function finish(){
    loading.classList.remove('now_loading');
    done.classList.add('open');
  }

  openButton.addEventListener('click', () => {
    modal.classList.add('openModal');
  });
  closeButton.addEventListener('click', () => {
    modal.classList.remove('openModal');
  });
  inputDay.addEventListener('click', () => {
    calendar.classList.add('openCalendar');
    modalContainer.classList.add('hide');
  });
  backButton.addEventListener('click', () => {
    calendar.classList.remove('openCalendar')
    modalContainer.classList.remove('hide')
  });
  calendarButton.addEventListener('click', () => {
    calendar.classList.remove('openCalendar')
    modalContainer.classList.remove('hide')
  })
  modalRecord.addEventListener('click', () => {
    modalContainer.classList.add('hide');
    loading.classList.add('now_loading');
    setTimeout(finish, 3000);
    if (shareButton.className == 'checked') {
      const left = Math.round(window.screen.width / 2 - 275);
      const top = (window.screen.height > 420) ? Math.round(window.screen.height / 2 - 210) : 0;
      window.open(
        "https://twitter.com/intent/tweet?text=" + encodeURIComponent(twitterInput.value),
        null,
        "scrollbars=yes,resizable=yes,toolbar=no,location=yes,width=550,height=420,left=" + left + ",top=" + top
      );
    }
  })
  closeButton2.addEventListener('click', () => {
    modal.classList.remove('openModal');
  });
  closeButton3.addEventListener('click', () => {
    modal.classList.remove('openModal');
    modalContainer.classList.remove('hide')
    done.classList.remove('open');
  });
  shareButton.addEventListener('click', () => {
    shareButton.classList.toggle('checked');
  })
}