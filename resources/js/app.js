import './bootstrap';
import { bootFcm } from './fcm';

// console.log("📦 app.js loaded!");

bootFcm(window.Laravel?.user?.id);