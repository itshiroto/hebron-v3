import { defineStore } from 'pinia';

export const useNavigationStore = defineStore('navigation', {
    state: () => ({
        menuItems: [
            { name: "ABOUT US", href: "/about" },
            { name: "DIVISIONS", href: "/division" },
            { name: "MINISTRIES", href: "/ministry" },
            { name: "DEVOTIONS", href: "/devotion" },
            { name: "CONTACT US", href: "/contact" },
        ],
    }),
    getters: {
        getMenuItems: (state) => state.menuItems,
    },
});
