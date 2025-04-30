import 'slick-carousel';
import 'bootstrap';
import toastr from 'toastr';
import './jquery-passive.js';

console.error('load app.js');

window.toastr = toastr; // Присваиваем toastr к window

class App {
    currentView = null;

    constructor() {
        console.log('load view app.js');
        $(document).ready(() => {
            this.loadView();
        });

        return this;
    }

    loadView() {
        this.dispatchView();
    }

    async dispatchView() {
        const routeName = frontConfig.routeName;
        const map = {
            'front.index': 'index',
            'auth.register': 'register',
            'account.activation.index': 'register',
            'account.fill.index': 'register',
            'account.payments.index': 'payment',
        };

        const viewName = map[routeName] || 'base';
        console.log('load view name:', viewName);
        try {
            const { default: View } = await import(`./views/${viewName}.js`);
            this.currentView = new View();
        } catch (error) {
            console.error(`Failed to load view: ${viewName}`, error);
        }
    }
}

export default new App();
