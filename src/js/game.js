/**
 * Solitaire Game - MVC Pattern
 */

// --- MODEL ---

class Card {
    constructor(suit, value) {
        this.suit = suit;
        this.value = value; // 'AS', '2'...'K'
        this.numValue = this.calculateNumValue(value);
        this.color = (suit === 'corazones' || suit === 'diamantes') ? 'red' : 'black';
        this.image = `${value}${suit}.svg`;
        this.faceUp = false;
        this.id = `${value}-${suit}`; // Unique ID for tracking
    }

    calculateNumValue(value) {
        if (value === 'AS') return 1;
        if (value === 'J') return 11;
        if (value === 'Q') return 12;
        if (value === 'K') return 13;
        return parseInt(value);
    }

    flip(faceUp = true) {
        this.faceUp = faceUp;
    }
}

class GameModel {
    constructor() {
        this.suits = ['corazones', 'diamantes', 'picas', 'trevoles'];
        this.values = ['AS', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
        
        this.stock = [];
        this.waste = [];
        this.foundations = {
            corazones: [],
            diamantes: [],
            picas: [],
            trevoles: []
        };
        this.tabla = Array(7).fill(null).map(() => []);
        this.timerStarted = false;
        this.startTime = null;
        this.endTime = null;
    }

    init() {
        // Create and shuffle deck
        let deck = [];
        this.suits.forEach(suit => {
            this.values.forEach(val => {
                deck.push(new Card(suit, val));
            });
        });
        
        // Fisher-Yates Shuffle
        for (let i = deck.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [deck[i], deck[j]] = [deck[j], deck[i]];
        }

        // Deal
        let cardIdx = 0;
        for (let i = 0; i < 7; i++) {
            for (let j = 0; j <= i; j++) {
                let card = deck[cardIdx++];
                if (j === i) card.flip(true);
                this.tabla[i].push(card);
            }
        }
        this.stock = deck.slice(cardIdx);
    }

    // --- Logic Methods ---

    drawCard() {
        if (this.stock.length > 0) {
            let card = this.stock.pop();
            card.flip(true);
            this.waste.push(card);
        } else if (this.waste.length > 0) {
            // Reset stock
            this.stock = this.waste.reverse().map(c => { c.flip(false); return c; });
            this.waste = [];
        }
        return true; // Action taken
    }

    moveCards(sourceType, sourceIdx, targetType, targetIdx, count = 1) {
        let cardsToMove = [];
        
        // Remove
        if (sourceType === 'waste') cardsToMove = [this.waste.pop()];
        else if (sourceType === 'foundation') cardsToMove = [this.foundations[sourceIdx].pop()];
        else if (sourceType === 'tabla') {
            let col = this.tabla[sourceIdx];
            cardsToMove = col.splice(col.length - count, count);
            if (col.length > 0) col[col.length-1].flip(true);
        }

        // Add
        if (targetType === 'foundation') this.foundations[targetIdx].push(cardsToMove[0]);
        else if (targetType === 'tabla') this.tabla[targetIdx].push(...cardsToMove);
        
        return true;
    }

    isValidMove(card, targetType, targetIdx) {
        if (targetType === 'foundation') {
            // Must be same suit
            if (card.suit !== targetIdx) return false;
            let pile = this.foundations[targetIdx];
            let topVal = pile.length > 0 ? pile[pile.length-1].numValue : 0;
            return card.numValue === topVal + 1;
        } else if (targetType === 'tabla') {
            let col = this.tabla[targetIdx];
            if (col.length === 0) return card.numValue === 13; // King
            let top = col[col.length-1];
            return top.color !== card.color && top.numValue === card.numValue + 1;
        }
        return false;
    }

    checkWinCondition() {
        return Object.values(this.foundations).every(f => f.length === 13);
    }
}

// --- VIEW ---

class GameView {
    constructor() {
        this.elements = {
            stock: document.getElementById('stock'),
            waste: document.getElementById('waste'),
            timer: document.getElementById('timer-display'),
            foundations: {
                corazones: document.getElementById('foundation-hearts'),
                diamantes: document.getElementById('foundation-diamonds'),
                trevoles: document.getElementById('foundation-clubs'),
                picas: document.getElementById('foundation-spades')
            },
            tabla: Array.from({length: 7}, (_, i) => document.getElementById(`tabla-${i}`))
        };
        this.assetPath = '../assets/baraja_Francesa/';
        this.preloadImages();
    }

    preloadImages() {
        // Preload basic assets to avoid flickering
        const img = new Image();
        img.src = `${this.assetPath}Reverso1.svg`;
    }

    render(model, dirtyZones = null) {
        requestAnimationFrame(() => {
            if (!dirtyZones) {
                // Full Render
                this.renderPile(this.elements.stock, model.stock, 'stock');
                this.renderPile(this.elements.waste, model.waste, 'waste');
                Object.keys(this.elements.foundations).forEach(suit => {
                    this.renderPile(this.elements.foundations[suit], model.foundations[suit], 'foundation', suit);
                });
                this.elements.tabla.forEach((el, idx) => {
                    this.renderTablaColumn(el, model.tabla[idx], idx);
                });
            } else {
                // Partial Render
                dirtyZones.forEach(zone => {
                    if (zone.type === 'stock') this.renderPile(this.elements.stock, model.stock, 'stock');
                    else if (zone.type === 'waste') this.renderPile(this.elements.waste, model.waste, 'waste');
                    else if (zone.type === 'foundation') {
                        this.renderPile(this.elements.foundations[zone.index], model.foundations[zone.index], 'foundation', zone.index);
                    }
                    else if (zone.type === 'tabla') {
                        this.renderTablaColumn(this.elements.tabla[zone.index], model.tabla[zone.index], zone.index);
                    }
                });
            }
        });
    }

    updateTimer(timeString) {
        if (this.elements.timer) this.elements.timer.textContent = timeString;
    }

    getCardUrl(card) {
        return `url('${this.assetPath}${card.faceUp ? card.image : 'Reverso1.svg'}')`;
    }

    // Advanced: Syncs DOM nodes instead of destroying them (Virtual DOM alike)
    syncDom(container, cards, type, index) {
        let existingChildren = Array.from(container.children);
        
        cards.forEach((card, i) => {
            let el = existingChildren[i];
            let desiredUrl = this.getCardUrl(card);
            
            // Create if not exists
            if (!el) {
                el = document.createElement('div');
                el.className = 'card';
                container.appendChild(el);
            }

            // Update visuals ONLY if changed (Performance Booster)
            if (el.style.backgroundImage !== desiredUrl) {
                el.style.backgroundImage = desiredUrl;
            }

            // Update Layout
            if (type === 'tabla') {
                el.style.top = `${i * 30}px`;
            } else {
                el.style.top = '0px';
            }

            // Update Logic Data
            if (el.dataset.suit !== card.suit) el.dataset.suit = card.suit || '';
            if (el.dataset.num != card.numValue) el.dataset.num = card.numValue || 0;
            
            // Static metadata
            el.dataset.type = type;
            el.dataset.index = index !== null ? index : '';
            el.dataset.color = card.color || '';

            // Draggable State
            let isDraggable = false;
            // Only tableau face-up is draggable naturally
            // Stock dummy is never draggable
            if (type === 'tabla' && card.faceUp) isDraggable = true;
            if (type === 'waste' || type === 'foundation') isDraggable = true;
            if (type === 'stock') isDraggable = false;

            if (el.draggable !== isDraggable) el.draggable = isDraggable;
        });

        // Remove excess nodes
        while (container.children.length > cards.length) {
            container.removeChild(container.lastChild);
        }
    }

    renderPile(container, cards, type, index = null) {
        let visibleCards = [];
        if (type === 'stock') {
            if (cards.length === 0) {
                container.classList.add('empty-stock');
            } else {
                container.classList.remove('empty-stock');
                visibleCards = [{faceUp: false, suit: 'stock', numValue: 0}];
            }
        } else {
            if (cards.length > 0) {
                visibleCards = [cards[cards.length - 1]];
            }
        }
        this.syncDom(container, visibleCards, type, index);
    }

    renderTablaColumn(container, cards, colIndex) {
        // Tableau shows all cards
        this.syncDom(container, cards, 'tabla', colIndex);
    }
}

// --- CONTROLLER ---

class GameController {
    constructor(model, view) {
        this.model = model;
        this.view = view;
        this.timerInterval = null;
        
        this.init();
    }

    init() {
        this.model.init();
        this.setupEventListeners();
        this.view.render(this.model);
    }

    startTimer() {
        if (this.model.timerStarted) return;
        this.model.timerStarted = true;
        this.model.startTime = Date.now();
        
        this.timerInterval = setInterval(() => {
            let elapsed = Math.floor((Date.now() - this.model.startTime) / 1000);
            let minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
            let seconds = (elapsed % 60).toString().padStart(2, '0');
            this.view.updateTimer(`${minutes}:${seconds}`);
        }, 1000);
    }

    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    handleAction() {
        if (!this.model.timerStarted) {
            this.startTimer();
        }
    }

    setupEventListeners() {
        // Stock click
        this.view.elements.stock.onclick = () => {
            this.handleAction();
            this.model.drawCard();
            this.view.render(this.model, [{type: 'stock'}, {type: 'waste'}]);
        };

        // Global Drag & Drop Delegation
        document.body.addEventListener('dragstart', (e) => this.handleDragStart(e));
        document.body.addEventListener('dragover', (e) => e.preventDefault());
        document.body.addEventListener('drop', (e) => this.handleDrop(e));
        
        // Global Double Click
        document.body.addEventListener('dblclick', (e) => this.handleDoubleClick(e));
    }

    handleDragStart(e) {
        if (!e.target.classList.contains('card')) return;
        
        let type = e.target.dataset.type;
        let index = e.target.dataset.index;
        
        // Identify the card logic object
        // For simplicity, we reconstruct minimal info needed for validation
        // or we can pass the coordinates (type, index, depth)
        
        // Calculate how many cards form the stack (for tableau)
        let count = 1;
        if (type === 'tabla') {
             // We need to know which card within the column it is
             // We can find it by DOM position
             let siblings = Array.from(e.target.parentNode.children);
             let idxInDom = siblings.indexOf(e.target);
             count = siblings.length - idxInDom;
        }

        e.dataTransfer.setData('text/plain', JSON.stringify({
            sourceType: type,
            sourceIndex: index,
            cardVal: parseInt(e.target.dataset.num),
            cardSuit: e.target.dataset.suit,
            cardColor: e.target.dataset.color,
            count: count
        }));
    }

    handleDrop(e) {
        e.preventDefault();
        let dropTarget = e.target.closest('.pile, .column');
        if (!dropTarget) return;

        let rawData = e.dataTransfer.getData('text/plain');
        if (!rawData) return;
        let data = JSON.parse(rawData);

        // Determine destination
        let destType = dropTarget.classList.contains('foundation') ? 'foundation' : 'tabla';
        let destIdx = destType === 'foundation' ? dropTarget.dataset.suit : parseInt(dropTarget.id.split('-')[1]);

        // Validate
        let dummyCard = { 
            numValue: data.cardVal, 
            suit: data.cardSuit, 
            color: data.cardColor 
        };

        // Foundation only takes 1 card
        if (destType === 'foundation' && data.count > 1) return;

        if (this.model.isValidMove(dummyCard, destType, destIdx)) {
            this.handleAction(); // Start timer if needed

            let sIdx = data.sourceType === 'tabla' ? parseInt(data.sourceIndex) : data.sourceIndex;
            
            this.model.moveCards(
                data.sourceType, 
                sIdx, 
                destType, 
                destIdx, 
                data.count
            );
            
            // Partial Render logic
            let dirty = [
                {type: data.sourceType, index: sIdx},
                {type: destType, index: destIdx}
            ];
            this.view.render(this.model, dirty);

            if (this.model.checkWinCondition()) {
                this.stopTimer();
                setTimeout(() => alert("¡Felicidades! Has ganado."), 100);
            }
        }
    }

    handleDoubleClick(e) {
        let cardEl = e.target.closest('.card');
        if (!cardEl) return;
        
        let type = cardEl.dataset.type;
        if (type === 'stock') return; // Handled by click
        if (!cardEl.draggable) return; // Only face up

        let index = cardEl.dataset.index;
        // Fix index parsing
        if (type === 'tabla') index = parseInt(index);

        let card = {
            numValue: parseInt(cardEl.dataset.num),
            suit: cardEl.dataset.suit,
            color: cardEl.dataset.color
        };

        // Try to move to foundation
        if (this.model.isValidMove(card, 'foundation', card.suit)) {
            // Check if it is indeed the last card if tableau
            if (type === 'tabla') {
                let col = this.model.tabla[index];
                let last = col[col.length-1];
                if (last.numValue !== card.numValue || last.suit !== card.suit) return;
            }

            this.handleAction(); // Start timer
            this.model.moveCards(type, index, 'foundation', card.suit, 1);
            
            // Partial Render
            this.view.render(this.model, [
                {type: type, index: index},
                {type: 'foundation', index: card.suit}
            ]);

            if (this.model.checkWinCondition()) {
                this.stopTimer();
                setTimeout(() => alert("¡Felicidades! Has ganado."), 100);
            }
        }
    }
}

// --- INITIALIZATION ---

document.addEventListener('DOMContentLoaded', () => {
    const model = new GameModel();
    const view = new GameView();
    const controller = new GameController(model, view);
});