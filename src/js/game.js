(() => {
'use strict';

const SUITS = ['corazones', 'diamantes', 'picas', 'trevoles'];
const RED_SUITS = new Set(['corazones', 'diamantes']);
const ASSETS = '../assets/baraja_Francesa';
const BACK_IMG = `${ASSETS}/Reverso1.svg`;
const CARD_OFFSET = 20;

// Pre-compute rank labels
const RANK_LABELS = ['', 'AS', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

// Game state
let tableau = [];
let foundations = [[], [], [], []];
let stock = [];
let waste = [];
let moves = 0;
let finished = false;

// DOM refs (cached once)
let $stock, $waste, $foundations, $tableau, $moves, $timer, $modal, $endTitle, $endSummary, $dragLayer;

// Drag state
let dragData = null;
let dragRAF = null;
let dragX = 0, dragY = 0;

// Timer state
let startTime = 0;
let timerInterval = null;
let gameStarted = false;

// Card element pool
const cardPool = new Map();

// Single reusable stock back element
let $stockBack = null;

document.addEventListener('DOMContentLoaded', init);

function init() {
// Cache DOM once
$stock = document.getElementById('stock');
$waste = document.getElementById('waste');
$foundations = document.querySelectorAll('[data-dropzone="foundation"]');
$tableau = document.querySelectorAll('[data-dropzone="tableau"]');
$moves = document.getElementById('moves');
$timer = document.getElementById('timer');
$modal = document.getElementById('endModal');
$endTitle = document.getElementById('endTitle');
$endSummary = document.getElementById('endSummary');
$dragLayer = document.getElementById('drag-layer');

// Create stock back once
$stockBack = document.createElement('div');
$stockBack.className = 'card face-down';
$stockBack.style.cssText = `background-image:url(${BACK_IMG});top:0`;

// Wire events once
$stock.addEventListener('click', onStockClick);
document.getElementById('restartBtn').addEventListener('click', newGame);
document.getElementById('menuBtn').addEventListener('click', () => location.href = 'menu.php');
document.getElementById('playAgainBtn').addEventListener('click', newGame);

newGame();
}

function newGame() {
// Build and shuffle deck
const deck = [];
let id = 0;
for (const suit of SUITS) {
for (let rank = 1; rank <= 13; rank++) {
deck.push({
id: id++,
suit,
rank,
faceUp: false,
img: `${ASSETS}/${RANK_LABELS[rank]}${suit}.svg`
});
}
}
shuffleArray(deck);

// Deal tableau
tableau = [];
for (let col = 0; col < 7; col++) {
const pile = deck.splice(0, col + 1);
pile[pile.length - 1].faceUp = true;
tableau.push(pile);
}

stock = deck;
waste = [];
foundations = [[], [], [], []];
moves = 0;
finished = false;
gameStarted = false;

// Reset timer display
if (timerInterval) clearInterval(timerInterval);
timerInterval = null;
    $timer.textContent = '00:00.000';

    render();
    $modal.classList.add('hidden');
}

function startGame() {
    if (gameStarted) return;
    gameStarted = true;
    startTime = Date.now();
    timerInterval = setInterval(updateTimer, 31);
}

function updateTimer() {
    const diff = Date.now() - startTime;
    const m = Math.floor(diff / 60000).toString().padStart(2, '0');
    const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
    const ms = (diff % 1000).toString().padStart(3, '0');
    $timer.textContent = `${m}:${s}.${ms}`;
}

function shuffleArray(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = (Math.random() * (i + 1)) | 0;
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
}

// ─────────────────────────────────────────────────────────────
// Render - minimal DOM manipulation
// ─────────────────────────────────────────────────────────────

function render() {
renderStock();
renderWaste();
for (let i = 0; i < 4; i++) renderFoundation(i);
for (let i = 0; i < 7; i++) renderColumn(i);
$moves.textContent = moves;
}

function renderStock() {
if (stock.length) {
if (!$stockBack.parentNode) $stock.appendChild($stockBack);
} else {
if ($stockBack.parentNode) $stockBack.remove();
}
}

function renderWaste(animate = false) {
$waste.textContent = '';
if (!waste.length) return;
const card = waste[waste.length - 1];
const el = getCardEl(card);
applyCardStyle(el, card, 0, 'waste', null, waste.length - 1, null);
if (animate) {
el.classList.remove('flip-in');
// force reflow
void el.offsetWidth;
el.classList.add('flip-in');
} else {
el.classList.remove('flip-in');
}
$waste.appendChild(el);
}

function renderFoundation(idx) {
const slot = $foundations[idx];
slot.textContent = '';
const pile = foundations[idx];
if (!pile.length) return;
const card = pile[pile.length - 1];
const el = getCardEl(card);
applyCardStyle(el, card, 0, 'foundation', null, null, idx);
slot.appendChild(el);
}

function renderColumn(colIdx) {
const col = $tableau[colIdx];
const pile = tableau[colIdx];

// Sync DOM children with pile
// Remove extras
while (col.children.length > pile.length) {
col.lastChild.remove();
}

for (let i = 0; i < pile.length; i++) {
const card = pile[i];
const el = getCardEl(card);
// Ensure correct element is at position i
if (col.children[i] !== el) {
if (i < col.children.length) {
col.insertBefore(el, col.children[i]);
} else {
col.appendChild(el);
}
}
applyCardStyle(el, card, i * CARD_OFFSET, 'tableau', colIdx, i, null);
}
}

function getCardEl(card) {
let el = cardPool.get(card.id);
if (!el) {
el = document.createElement('div');
el.className = 'card';
el.style.top = '0'; // Ensure compatible with translate3d
el.addEventListener('pointerdown', onCardDown);
cardPool.set(card.id, el);
}
return el;
}

function applyCardStyle(el, card, top, origin, col, pos, foundation) {
// Update image only if changed
const img = card.faceUp ? card.img : BACK_IMG;
if (el._img !== img) {
el.style.backgroundImage = `url(${img})`;
el._img = img;
}

// Position optimization: use transform
if (el._top !== top) {
el.style.transform = `translate3d(0, ${top}px, 0)`;
el._top = top;
}

el.classList.toggle('face-down', !card.faceUp);

// Store meta for drag
el._card = card;
el._origin = origin;
el._col = col;
el._pos = pos;
el._foundation = foundation;
}

// ─────────────────────────────────────────────────────────────
// Stock click
// ─────────────────────────────────────────────────────────────

function onStockClick() {
if (finished) return;
startGame();
if (stock.length) {
const card = stock.pop();
card.faceUp = true;
waste.push(card);
moves++;
renderStock();
renderWaste(true);
} else if (waste.length) {
while (waste.length) {
const c = waste.pop();
c.faceUp = false;
stock.push(c);
}
moves++;
renderStock();
renderWaste();
}
$moves.textContent = moves;
setTimeout(checkEnd, 0); // Non-blocking check
}

// ─────────────────────────────────────────────────────────────
// Drag & Drop - direct pointer handlers, no RAF loop
// ─────────────────────────────────────────────────────────────

function onCardDown(e) {
if (finished) return;
const el = e.currentTarget;
const card = el._card;
if (!card || !card.faceUp) return;

e.preventDefault();

const origin = el._origin;
let cards;
if (origin === 'waste') {
cards = [card];
} else if (origin === 'tableau') {
cards = tableau[el._col].slice(el._pos);
} else if (origin === 'foundation') {
cards = [card];
} else {
return;
}

const rect = el.getBoundingClientRect();
const ghost = document.createElement('div');
ghost.className = 'drag-ghost';
ghost.style.cssText = `transform:translate(${rect.left}px,${rect.top}px); width: ${rect.width}px; height: ${rect.height}px`;
for (let i = 0; i < cards.length; i++) {
const c = cards[i];
const g = document.createElement('div');
g.className = 'card';
g.style.cssText = `background-image:url(${c.img});top:${i * CARD_OFFSET}px;pointer-events:none`;
ghost.appendChild(g);
}
$dragLayer.appendChild(ghost);

dragData = {
cards,
origin,
col: el._col,
pos: el._pos,
foundation: el._foundation,
ghost,
offsetX: e.clientX - rect.left,
offsetY: e.clientY - rect.top
};

highlightTargets(cards[0], cards.length);

document.addEventListener('pointermove', onDragMove);
document.addEventListener('pointerup', onDragEnd);
}

function onDragMove(e) {
if (!dragData) return;
dragX = e.clientX;
dragY = e.clientY;

if (dragRAF) return;

dragRAF = requestAnimationFrame(() => {
dragRAF = null;
if (!dragData) return;
dragData.ghost.style.transform = `translate3d(${dragX - dragData.offsetX}px,${dragY - dragData.offsetY}px, 0)`;
});
}

function onDragEnd(e) {
document.removeEventListener('pointermove', onDragMove);
document.removeEventListener('pointerup', onDragEnd);
if (dragRAF) {
cancelAnimationFrame(dragRAF);
dragRAF = null;
}
if (!dragData) return;

clearHighlights();
dragData.ghost.remove();

const target = document.elementFromPoint(e.clientX, e.clientY);
const dropZone = target?.closest('[data-dropzone]');

if (dropZone && tryMove(dropZone)) {
startGame();
moves++;
$moves.textContent = moves;
setTimeout(checkEnd, 0);
}

dragData = null;
}

function tryMove(dropZone) {
const lead = dragData.cards[0];
const zone = dropZone.dataset.dropzone;

if (zone === 'tableau') {
const colIdx = +dropZone.dataset.column;
if (canPlaceOnTableau(lead, colIdx)) {
removeFromOrigin();
tableau[colIdx].push(...dragData.cards);
renderColumn(colIdx);
return true;
}
} else if (zone === 'foundation' && dragData.cards.length === 1) {
const fIdx = +dropZone.dataset.foundation;
if (canPlaceOnFoundation(lead, fIdx)) {
removeFromOrigin();
foundations[fIdx].push(lead);
renderFoundation(fIdx);
return true;
}
}
return false;
}

function removeFromOrigin() {
const { origin, col, pos, foundation } = dragData;
if (origin === 'waste') {
waste.pop();
renderWaste();
} else if (origin === 'tableau') {
tableau[col].splice(pos);
const last = tableau[col][tableau[col].length - 1];
if (last && !last.faceUp) last.faceUp = true;
renderColumn(col);
} else if (origin === 'foundation') {
foundations[foundation].pop();
renderFoundation(foundation);
}
}

function canPlaceOnTableau(card, colIdx) {
const pile = tableau[colIdx];
if (!pile.length) return card.rank === 13;
const top = pile[pile.length - 1];
return top.faceUp && RED_SUITS.has(top.suit) !== RED_SUITS.has(card.suit) && top.rank === card.rank + 1;
}

function canPlaceOnFoundation(card, fIdx) {
const pile = foundations[fIdx];
if (!pile.length) return card.rank === 1;
const top = pile[pile.length - 1];
return top.suit === card.suit && card.rank === top.rank + 1;
}

// ─────────────────────────────────────────────────────────────
// Highlights
// ─────────────────────────────────────────────────────────────

function highlightTargets(card, len) {
for (let i = 0; i < 7; i++) {
if (canPlaceOnTableau(card, i)) $tableau[i].classList.add('hint-target');
}
if (len === 1) {
for (let i = 0; i < 4; i++) {
if (canPlaceOnFoundation(card, i)) $foundations[i].classList.add('hint-target');
}
}
}

function clearHighlights() {
for (let i = 0; i < 7; i++) $tableau[i].classList.remove('hint-target');
for (let i = 0; i < 4; i++) $foundations[i].classList.remove('hint-target');
}

// ─────────────────────────────────────────────────────────────
// End game
// ─────────────────────────────────────────────────────────────

function checkEnd() {
if (foundations.every(f => f.length === 13)) {
endGame(true);
} else if (!stock.length && !waste.length && !hasAnyMove()) {
endGame(false);
}
}

function hasAnyMove() {
if (stock.length) return true;

// Check waste top
const wTop = waste[waste.length - 1];
if (wTop && canMoveAnywhere(wTop, 1)) return true;

// Check tableau face-up stacks
for (const pile of tableau) {
for (let i = 0; i < pile.length; i++) {
if (pile[i].faceUp && canMoveAnywhere(pile[i], pile.length - i)) return true;
}
}
return false;
}

function canMoveAnywhere(card, len) {
for (let i = 0; i < 7; i++) if (canPlaceOnTableau(card, i)) return true;
if (len === 1) {
for (let i = 0; i < 4; i++) if (canPlaceOnFoundation(card, i)) return true;
}
return false;
}

function endGame(won) {
finished = true;
if (timerInterval) clearInterval(timerInterval);
const duration = Math.floor((Date.now() - startTime));

$endTitle.textContent = won ? '¡Victoria!' : 'Sin movimientos';
$endSummary.textContent = `Movimientos: ${moves} | Tiempo: ${$timer.textContent}`;
$modal.classList.remove('hidden');
fetch('../controllers/game.php', {
method: 'POST',
headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
body: `result=${won ? 'victoria' : 'derrota'}&moves=${moves}&timeMs=${duration}`
}).catch(() => {});
}
})();
