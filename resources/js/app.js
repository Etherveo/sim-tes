import './bootstrap';
import Alpine from 'alpinejs';

Alpine.store('cart', {
    // Ambil data dari localStorage saat pertama kali dimuat, atau objek kosong jika tidak ada
    items: JSON.parse(localStorage.getItem('cart_items')) || {},

    // Hitung total item unik
    get count() {
        return Object.keys(this.items).length;
    },

    // Fungsi pembantu untuk menyimpan ke browser
    persist() {
        localStorage.setItem('cart_items', JSON.stringify(this.items));
    },

    addItem(productId) {
        let newItems = { ...this.items };
        newItems[productId] = (newItems[productId] || 0) + 1;
        this.items = newItems;
        this.persist(); 
    },

    incrementItem(productId) {
        let newItems = { ...this.items };
        newItems[productId]++;
        this.items = newItems;
        this.persist(); 
    },

    decrementItem(productId) {
        let newItems = { ...this.items };
        if (newItems[productId] > 1) {
            newItems[productId]--;
        } else {
            delete newItems[productId];
        }
        this.items = newItems;
        this.persist(); 
    },

    removeItem(productId) {
        let newItems = { ...this.items };
        delete newItems[productId];
        this.items = newItems;
        this.persist(); 
    }
});

window.Alpine = Alpine;
Alpine.start();