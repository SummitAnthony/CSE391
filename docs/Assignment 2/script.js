// Converter
function convert() {
    const input = parseFloat(document.getElementById('converter_input').value);
    const unit = document.getElementById('converter_unit').value;
    let result = 0;

    if (isNaN(input)) {
        document.getElementById('converter_result').textContent = "Please enter a valid number.";
        return;
    }

    if (unit === 'lbtokg') {
        result = input * 0.453592;
    } else if (unit === 'kgtolb') {
        result = input * 2.20462;
    }

    document.getElementById('converter_result').textContent = `Converted Value: ${result.toFixed(2)}`;
}


// Calculator
function calculate() {
    const values = document.getElementById('calc-value').value.split(',').map(Number);
    if (values.length === 0 || values.some(isNaN)) {
        document.getElementById('max').textContent = "Error";
        document.getElementById('min').textContent = "Error";
        document.getElementById('sum').textContent = "Error";
        document.getElementById('average').textContent = "Error";
        return;
    }

    const max = Math.max(...values);
    const min = Math.min(...values);
    const sum = values.reduce((acc, val) => acc + val, 0);
    const average = sum / values.length;

    document.getElementById('max').textContent = max.toFixed(2);
    document.getElementById('min').textContent = min.toFixed(2);
    document.getElementById('sum').textContent = sum.toFixed(2);
    document.getElementById('average').textContent = average.toFixed(2);
}


// Magic Text Tools
function capitalizeText() {
    const input = document.getElementById('magic-input').value;
    document.getElementById('magic-input').value = input.toUpperCase();
}

function sortLines() {
    const textarea = document.getElementById('magic-input');
    const lines = textarea.value.split('\n');
    lines.sort();
    textarea.value = lines.join('\n');
}

function reverseLines() {
    var magicinput = document.getElementById("magic-input").value;
    var magicArray = magicinput.split("\n");
    var newmagicinput = "";
    
    // Reverse the array of lines and each line's letters
    magicArray.reverse();
    for (var i = 0; i < magicArray.length; i++) {
        var reversedLine = magicArray[i].split("").reverse().join(""); // Reverse the letters in each line
        if (i == magicArray.length - 1) {
            newmagicinput += reversedLine; // Avoid adding extra newline at the end
        } else {
            newmagicinput += reversedLine + "\n";
        }
    }
    
    document.getElementById("magic-input").value = newmagicinput;
    console.log("Reversed Lines and Letters!");
}


function stripBlank(){
    var magicinput = document.getElementById("magic-input").value;
    var magicArray = magicinput.split("\n");
    var newmagicinput = "";
    var newmagicarray = []
    // Remove the front spaces of any lines
    magicArray.forEach((magicline) => {
        if (magicline === ""){ 
            return
        }
        newmagicinput += magicline.trim() + "\n";
    })

    document.getElementById("magic-input").value = newmagicinput;
    console.log("Stripped Blank Lines!")
}

function addLineNo() {
    const textarea = document.getElementById('magic-input');
    const lines = textarea.value.split('\n');
    const numberedLines = lines.map((line, index) => `${index + 1}. ${line}`);
    textarea.value = numberedLines.join('\n');
}

function shuffleLines() {
    const textarea = document.getElementById('magic-input');
    const lines = textarea.value.split('\n');
    for (let i = lines.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [lines[i], lines[j]] = [lines[j], lines[i]];
    }
    textarea.value = lines.join('\n');
}
function cleartext() {
    document.getElementById("magic-input").value = "";
    console.log("Cleared Magic TextArea")
}
// Quotes
const quotes = [
    { text: "The only way to do great work is to love what you do.", author: "Steve Jobs" },
    { text: "Success is not final, failure is not fatal: It is the courage to continue that counts.", author: "Winston Churchill" },
    { text: "Believe you can and you're halfway there.", author: "Theodore Roosevelt" },
    { text: "Hardships often prepare ordinary people for an extraordinary destiny.", author: "C.S. Lewis" },
];

let currentQuoteIndex = 0;

function fetchQuote(next = true) {
    currentQuoteIndex = (next ? currentQuoteIndex + 1 : currentQuoteIndex - 1 + quotes.length) % quotes.length;
    document.getElementById('quote_text').textContent = quotes[currentQuoteIndex].text;
    document.getElementById('quote_author').textContent = `- ${quotes[currentQuoteIndex].author}`;
}

// Change color, border, and font styles dynamically
const colorButtons = document.querySelectorAll('.color-btn');
const quotesBox = document.querySelector('.quotes-box');

colorButtons.forEach(button => {
    button.addEventListener('click', () => {
        const color = button.dataset.color;
        quotesBox.style.borderColor = color;
        quotesBox.style.backgroundColor = `${color}20`; // Light background with opacity
        quotesBox.style.color = color;
        quotesBox.style.fontFamily = 'Georgia, serif';
        quotesBox.style.fontSize = '22px';
    });
});

