console.log("Script chargé");

document.querySelectorAll('.button.finna').forEach(button => {
    let div = document.createElement('div'),
        letters = button.textContent.trim().split('');

    letters.forEach((letter, index, array) => {
        let element = document.createElement('span');
        element.innerHTML = letter.trim() ? letter : '&nbsp;';
        div.appendChild(element);
    });

    button.innerHTML = div.outerHTML;

    button.addEventListener('mouseenter', () => {
        if (!button.classList.contains('in')) {
            button.classList.add('in');
        }
    });

    button.addEventListener('mouseleave', () => {
        if (button.classList.contains('in')) {
            button.classList.remove('in');
        }
    });
});
