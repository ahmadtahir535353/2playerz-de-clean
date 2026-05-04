@extends('front_new.layouts.app')
@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        .container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 280px minmax(450px, 700px) 380px;
            gap: 40px;
            align-items: start;
        }

        /* Left sidebar styles */
        .settings-sidebar {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .style-selector {
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .format-selector {
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .style-group {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .style-group:not(#formatGroup) {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .style-group:not(#formatGroup) .style-option {
            display: none;
        }

        .style-group:not(#formatGroup) .style-group-label:after {
            transform: rotate(-90deg);
        }

        .style-group-label {
            padding: 12px;
            background: #f8f9fa;
            cursor: pointer;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .style-group-label:after {
            content: '▼';
            font-size: 12px;
            transition: transform 0.3s;
        }

        .style-group.collapsed .style-group-label:after {
            transform: rotate(-90deg);
        }

        .style-group.collapsed .style-option {
            display: none;
        }

        .style-option {
            padding: 12px 16px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .style-option.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        /* Center video preview styles */
        .video-preview {
            width: 450px;
            height: 800px;
            margin: 0 auto;
            transition: all 0.3s ease;
            background: #000;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            max-width: 100%;
            cursor: pointer;
        }

        .video-preview.horizontal {
            width: 800px;
            height: 450px;
            max-width: 100%;
        }

        .video-preview.square {
            width: 600px;
            height: 600px;
            max-width: 100%;
        }

        .video-preview.portrait {
            width: 600px;
            height: 900px;
            max-width: 100%;
        }

        .video-preview.widescreen {
            width: 1260px;
            height: 540px;
            max-width: 100%;
        }

        .video-preview.cinematic {
            width: 1175px;
            height: 500px;
            max-width: 100%;
        }

        .video-preview.instagram {
            width: 450px;
            height: 562.5px;
            max-width: 100%;
        }

        .video-preview.sticky-preview {
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        /* Right sidebar styles */
        .right-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 380px;
            min-width: 380px;
        }

        .preview-controls {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            justify-content: space-between;
        }

        .playback-controls {
            display: flex;
            gap: 12px;
        }

        .project-controls {
            display: flex;
            gap: 8px;
        }

        .projects-list {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
        }

        .project-name-input {
            padding: 4px 8px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            width: 200px;
        }

        .project-content {
            padding: 12px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-top: 8px;
            display: none;
        }

        .project-content.expanded {
            display: block;
        }

        .project-content .controls {
            margin: 12px 0;
            display: flex;
            gap: 8px;
        }

        .project-content .add-remove-btn {
            padding: 4px 8px;
            font-size: 12px;
            min-width: 80px;
        }

        .bulk-controls {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .audio-inputs {
            display: flex;
            flex-direction: row;
            gap: 12px;
        }

        .audio-drop {
            padding: 12px;
            height: 40px;
            border: 2px dashed #e9ecef;
            border-radius: 12px;
            background: white;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 14px;
            min-height: 40px;
            position: relative;
        }

        .project-content > .audio-inputs {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .audio-drop {
            flex: 1 1 45%;
            min-width: 150px;
            height: 60px;
        }

        .project-content .controls {
            margin: 12px 0;
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .audio-inputs-row {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .audio-drop:hover {
            border-color: #2563eb;
            background: #f8f9fa;
        }

        .audio-drop.filled {
            border-style: solid;
            border-color: #2563eb;
        }

        .audio-drop .audio-name {
            font-size: 12px;
            margin-top: 8px;
            word-break: break-all;
        }

        .beat-sync-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .beat-sync-toggle.active {
            background: #2563eb;
        }

        .volume-control {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            padding: 0 4px;
        }

        .volume-label {
            font-size: 12px;
            color: #666;
            min-width: 60px;
        }

        .volume-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            outline: none;
        }

        .volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2563eb;
            cursor: pointer;
        }

        .volume-slider::-moz-range-thumb {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2563eb;
            cursor: pointer;
            border: none;
        }

        .image-placeholders-container {
            height: auto;  
            overflow-y: visible; 
            padding-right: 12px;
            border-radius: 12px;
            background: white;
            max-width: 100%;
        }

        .image-placeholders {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding: 4px;
            max-width: 100%;
            position: relative;
        }

        /* Update placeholder styling for consistent size */
        .placeholder {
            aspect-ratio: 9/16 !important; 
            min-height: 100px;
            max-height: 120px;
            background: white;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8px;
            position: relative;
            overflow: hidden;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .duration-input {
            position: absolute;
            bottom: 8px;
            left: 8px;
            right: 35%;
            width: calc(65% - 8px);
            padding: 4px;
            font-size: 12px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            background: rgba(255,255,255,0.9);
            z-index: 2;
        }

        .global-duration {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
        }

        .global-duration button {
            padding: 4px 8px;
            margin-left: 8px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            background: white;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .global-duration button:hover {
            background: #f8f9fa;
            transform: scale(1.1);
        }

        .global-duration input {
            width: 80px;
            padding: 4px 8px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }

        .global-duration label {
            font-size: 14px;
        }

        .placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            padding: 0;
        }

        /* Button styles */
        .controls {
            display: flex;
            flex-direction: row;
            gap: 8px;
            margin: 24px 0;
        }

        button {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
              135deg, 
              rgba(255,255,255,0.3) 0%,
              rgba(255,255,255,0.1) 25%,
              transparent 50%,
              rgba(255,255,255,0.1) 75%,
              rgba(255,255,255,0.3) 100%
            );
            animation: starry 5s linear infinite;
            z-index: 0;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .render-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .render-btn::before {
            background: linear-gradient(
              135deg, 
              rgba(255,255,255,0.4) 0%,
              rgba(255,255,255,0.2) 25%,
              transparent 50%,
              rgba(255,255,255,0.2) 75%,
              rgba(255,255,255,0.4) 100%
            );
        }

        .add-remove-btn {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
        }

        @keyframes starry {
            0% { background-position: 0 0; }
            100% { background-position: 200% 100%; }
        }

        #downloadLink {
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            margin: 20px 0;
            overflow: hidden;
            display: none;
        }

        .progress {
            width: 0%;
            height: 100%;
            background-color: #4CAF50;
            transition: width 0.3s;
        }

        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transform-origin: center;
            transition: all 0.5s ease-in-out;
        }

        .preview-image.transition-fade {
            transition: opacity 0.5s ease-in-out;
        }

        .preview-image.transition-crossfade {
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }

        .preview-image.transition-scale {
            opacity: 0;
            transform: scale(1.2);
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        }

        .preview-image.active {
            opacity: 1;
            transform: scale(1);
        }

        .preview-image.filter-grayscale {
            filter: grayscale(100%);
        }

        .preview-image.filter-sepia {
            filter: sepia(100%);
        }

        .preview-image.filter-vintage {
            filter: sepia(50%) contrast(95%) brightness(95%);
        }

        .preview-image.filter-blur {
            filter: blur(2px);
        }

        .preview-image.filter-brighten {
            filter: brightness(130%);
        }

        .preview-image.filter-dark {
            filter: brightness(70%);
        }

        .preview-image.filter-contrast {
            filter: contrast(150%);
        }

        .preview-image.filter-hueRotate {
            filter: hue-rotate(90deg);
        }

        .preview-image.filter-invert {
            filter: invert(100%);
        }

        .preview-image.filter-saturate {
            filter: saturate(200%);
        }

        .preview-image.filter-coldBlue {
            filter: saturate(120%) hue-rotate(180deg);
        }

        .preview-image.filter-warmOrange {
            filter: saturate(120%) hue-rotate(-30deg);
        }

        .preview-image.filter-cyberpunk {
            filter: hue-rotate(270deg) saturate(200%) brightness(120%);
        }

        .preview-image.filter-noir {
            filter: grayscale(100%) contrast(150%) brightness(90%);
        }

        .preview-image.filter-oldMovie {
            filter: sepia(50%) contrast(85%) brightness(90%) grayscale(50%);
        }

        .preview-image.filter-dramatic {
            filter: contrast(150%) brightness(90%) saturate(120%);
        }

        .preview-image.filter-matrix {
            filter: brightness(120%) saturate(150%) hue-rotate(100deg);
        }

        .preview-image.filter-sunset {
            filter: sepia(30%) saturate(150%) hue-rotate(-20deg);
        }

        .preview-image.filter-neon {
            filter: brightness(120%) contrast(120%) saturate(200%) hue-rotate(5deg);
        }

        @keyframes gentlePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes continuousZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.2); }
        }

        @keyframes kenBurns {
            0% { 
                transform: scale(1) translate(0, 0); 
            }
            50% { 
                transform: scale(1.1) translate(-2%, -2%); 
            }
            100% { 
                transform: scale(1) translate(0, 0); 
            }
        }

        @keyframes slideZoom {
            0% { transform: scale(1) translateX(0); }
            50% { transform: scale(1.2) translateX(20px); }
            100% { transform: scale(1) translateX(0); }
        }

        @keyframes rotateZoom {
            0% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.2) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }

        @keyframes bounceZoom {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
        }

        @keyframes spiralZoom {
            0% { transform: scale(1) rotate(0deg) translateX(0); }
            50% { transform: scale(1.3) rotate(180deg) translateX(30px); }
            100% { transform: scale(1) rotate(360deg) translateX(0); }
        }

        @keyframes waveZoom {
            0% { transform: scale(1) translateY(0); }
            50% { transform: scale(1.2) translateY(20px); }
            100% { transform: scale(1) translateY(0); }
        }

        @keyframes diagonalZoom {
            0% { transform: scale(1) translate(0, 0); }
            50% { transform: scale(1.2) translate(20px, 20px); }
            100% { transform: scale(1) translate(0, 0); }
        }

        @keyframes heartbeatZoom {
            0%, 100% { transform: scale(1); }
            40% { transform: scale(1.15); }
            80% { transform: scale(1.3); }
        }

        @keyframes swingZoom {
            0% { transform: scale(1) rotate(0deg); }
            25% { transform: scale(1.1) rotate(15deg); }
            75% { transform: scale(1.2) rotate(-15deg); }
            100% { transform: scale(1) rotate(0deg); }
        }

        @keyframes pulseRotateZoom {
            0% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.2) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }

        @keyframes elasticZoom {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); animation-timing-function: cubic-bezier(0.32, 0, 0.67, 1.55); }
            100% { transform: scale(1); }
        }

        @keyframes randomZoom {
            0%, 100% { transform: scale(1); }
            20% { transform: scale(1.1) translate(10px, -10px); }
            40% { transform: scale(1.2) translate(-15px, 5px); }
            60% { transform: scale(1.15) translate(5px, 15px); }
            80% { transform: scale(1.25) translate(-5px, -5px); }
        }

        @keyframes zigzagZoom {
            0% { transform: scale(1) translate(0, 0); }
            25% { transform: scale(1.1) translate(20px, -20px); }
            50% { transform: scale(1.2) translate(0, 0); }
            75% { transform: scale(1.1) translate(-20px, 20px); }
            100% { transform: scale(1) translate(0, 0); }
        }

        .preview-image.transition-slideLeft {
            transform: translateX(-100%);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-slideRight {
            transform: translateX(100%);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-slideUp {
            transform: translateY(-100%);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-slideDown {
            transform: translateY(100%);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-rotate {
            transform: rotate(-180deg);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-flip {
            transform: rotateY(180deg);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-diagonal {
            transform: translate(-100%, -100%);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-bounce {
            animation: bounceOut 0.5s ease-in-out;
        }

        .preview-image.transition-spiral {
            transform: rotate(360deg) scale(0);
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }

        .preview-image.transition-elastic {
            animation: elasticOut 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .preview-image.transition-swirl {
            transform: rotate(720deg) scale(0);
            transition: transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.8s ease-in-out;
        }

        .preview-image.transition-fold {
            transform: perspective(800px) rotateX(-90deg);
            transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out;
        }

        /* Improve scrollbar styling */
        .image-placeholders-container::-webkit-scrollbar {
            width: 8px;
        }

        .image-placeholders-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .image-placeholders-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .image-placeholders-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .total-duration {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .fullscreen-btn {
            display: none;
        }

        .remove-image-btn {
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            font-size: 12px;
            border: none;
            padding: 0;
        }

        .remove-image-btn:hover {
            background: rgba(255, 0, 0, 0.8);
            color: white;
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
        }

        .project-selector {
            width: 16px;
            height: 16px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            margin-right: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .project-selector.active {
            background: #2563eb;
        }

        .sticker-gallery-nav {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .sticker-gallery-nav:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .sticker-timing-btn {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .sticker-timing-btn.active {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .sticky-preview-btn {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .sticky-preview-btn.active {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        /* Add watermark styles */
        .watermark-section {
            margin-top: 16px;
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .watermark-header {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .watermark-toggle {
            width: 20px;
            height: 20px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .watermark-toggle.active {
            background: #2563eb;
        }

        .watermark-content {
            display: none;
        }

        .watermark-content.expanded {
            display: block;
        }

        .watermark-input {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }

        .watermark-drop {
            display: none;
        }

        .watermark-preview {
            position: absolute;
            cursor: move;
            z-index: 100;
            pointer-events: auto;
            user-select: none;
            resize: both;
            overflow: auto;
            min-width: 50px;
            min-height: 20px;
        }

        .watermark-image {
            max-width: 150px;
            max-height: 150px;
            width: 100%;
            height: 100%;
            resize: both;
            overflow: auto;
        }

        .watermark-text {
            padding: 8px;
            background: rgba(255,255,255,0.7);
            border-radius: 4px;
            font-size: 16px;
            font-family: 'Inter';
            resize: both;
            overflow: auto;
            min-width: 50px;
            min-height: 20px;
        }

        .watermark-font-size {
            width: 100%;
            padding: 8px;
            margin: 12px 0;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 14px;
        }

        .watermark-color {
            width: 100%;
            padding: 8px;
            margin: 12px 0;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }

        .watermark-bg-toggle {
            margin: 12px 0;
            display: flex;
            align-items: center;
        }

        .watermark-bg-toggle label {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 8px 16px;
            border-radius: 8px;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .watermark-toggles {
            display: flex;
            gap: 16px;
            margin: 12px 0;
        }

        .watermark-toggle-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .watermark-toggle {
            width: 20px;
            height: 20px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }

        .watermark-toggle.active {
            background: #2563eb;
        }

        .toggle-label {
            font-size: 12px;
            color: #666;
        }

        .watermark-preview-container {
            position: relative;
            width: 100%;
            height: 120px;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            margin: 12px 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .watermark-preview-container:hover {
            border-color: #2563eb;
            background: #f8f9fa;
        }

        .watermark-preview-container.filled {
            border-style: solid;
            border-color: #2563eb;
        }

        .watermark-preview-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .remove-watermark-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            font-size: 12px;
            border: none;
            padding: 0;
        }

        .remove-watermark-btn:hover {
            background: rgba(255, 0, 0, 0.8);
            color: white;
        }

        /* Add after watermark styles */
        .subtitles-section {
            margin-top: 16px;
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .subtitles-header {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .subtitles-toggle {
            width: 20px;
            height: 20px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }

        .subtitles-toggle.active {
            background: #2563eb;
        }

        .subtitles-content {
            display: none;
        }

        .subtitles-content.expanded {
            display: block;
        }

        .subtitles-preview {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 16px;
            z-index: 10;
            text-align: center;
            max-width: 80%;
            display: none;
        }

        /* Add after .subtitles-content styles */
        .font-selector {
            width: 100%;
            padding: 8px;
            margin: 12px 0;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 14px;
            background: white;
        }

        .subtitle-toggles {
            display: flex;
            gap: 16px;
            align-items: center;
            margin: 12px 0;
            justify-content: space-between;
            max-width: 300px;
        }

        .subtitle-toggle-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .subtitle-toggle {
            width: 20px;
            height: 20px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }

        .subtitle-toggle.active {
            background: #2563eb;
        }

        .subtitle-toggle-label {
            font-size: 12px;
            color: #666;
        }

        /* Font preview styles */
        .font-option-inter { font-family: 'Inter', sans-serif; }
        .font-option-roboto { font-family: 'Roboto', sans-serif; }
        .font-option-opensans { font-family: 'Open Sans', sans-serif; }
        .font-option-montserrat { font-family: 'Montserrat', sans-serif; }
        .font-option-lato { font-family: 'Lato', sans-serif; }
        .font-option-poppins { font-family: 'Poppins', sans-serif; }
        .font-option-raleway { font-family: 'Raleway', sans-serif; }
        .font-option-playfair { font-family: 'Playfair Display', serif; }
        .font-option-quicksand { font-family: 'Quicksand', sans-serif; }
        .font-option-merriweather { font-family: 'Merriweather', serif; }
        .preview-image.effect-vhs {
            filter: saturate(160%) contrast(110%) brightness(90%);
            animation: vhsEffect 0.1s infinite;
        }

        .preview-image.effect-retro {
            filter: sepia(50%) hue-rotate(-20deg);
            animation: retroFlicker 2s infinite;
        }

        .preview-image.effect-tv {
            filter: brightness(110%) contrast(120%);
            animation: tvScan 10s linear infinite;
        }

        .preview-image.effect-glitch {
            animation: glitchEffect 0.3s infinite;
        }

        .preview-image.effect-pixelate {
            image-rendering: pixelated;
            filter: brightness(110%);
        }

        .preview-image.effect-polaroid {
            filter: saturate(130%) contrast(110%) brightness(110%);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .preview-image.effect-rainbow {
            animation: rainbowFilter 3s linear infinite;
        }

        .preview-image.effect-noise {
            filter: contrast(120%);
            animation: noiseEffect 0.2s infinite;
        }

        .preview-image.effect-acid {
            animation: acidEffect 3s infinite;
        }

        .preview-image.effect-underwater {
            filter: hue-rotate(180deg) saturate(160%) brightness(90%);
            animation: underwaterEffect 3s infinite;
        }

        .preview-image.effect-hologram {
            filter: brightness(120%) saturate(150%);
            animation: hologramEffect 2s infinite;
        }

        .preview-image.effect-static {
            animation: staticEffect 0.1s infinite;
        }

        .preview-image.effect-nightvision {
            filter: brightness(120%) sepia(50%) hue-rotate(90deg);
        }

        .preview-image.effect-filmnoir {
            filter: grayscale(100%) contrast(150%) brightness(80%);
            animation: filmGrain 0.2s infinite;
        }

        .preview-image.effect-kaleidoscope {
            animation: kaleidoscopeEffect 3s infinite;
        }

        .preview-image.effect-lomography {
            filter: saturate(150%) contrast(120%) brightness(110%);
        }

        .preview-image.effect-technicolor {
            filter: saturate(200%) contrast(130%);
            animation: technicolorShift 4s infinite;
        }

        .preview-image.effect-cctv {
            filter: grayscale(100%) contrast(120%) brightness(90%);
            animation: cctvScan 2s linear infinite;
        }

        .preview-image.effect-heatmap {
            filter: hue-rotate(180deg) saturate(200%) contrast(150%);
            animation: heatmapPulse 2s infinite;
        }

        .preview-image.effect-timelapse {
            animation: timelapseEffect 1s infinite;
        }

        @keyframes vhsEffect {
            0% { transform: translateX(0); }
            50% { transform: translateX(1px); }
            100% { transform: translateX(0); }
        }

        @keyframes retroFlicker {
            0% { opacity: 1; }
            50% { opacity: 0.95; }
            100% { opacity: 1; }
        }

        @keyframes tvScan {
            0% { background-position: 0 0; }
            100% { background-position: 0 100%; }
        }

        @keyframes glitchEffect {
            0% { transform: translate(0); }
            25% { transform: translate(2px, -2px); }
            50% { transform: translate(-2px, 2px); }
            75% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }

        @keyframes rainbowFilter {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        @keyframes noiseEffect {
            0% { transform: translate(0, 0); }
            50% { transform: translate(1px, 1px); }
            100% { transform: translate(0, 0); }
        }

        @keyframes acidEffect {
            0% { filter: hue-rotate(0deg) saturate(200%); }
            100% { filter: hue-rotate(360deg) saturate(200%); }
        }

        @keyframes underwaterEffect {
            0% { transform: translateY(0); }
            50% { transform: translateY(2px); }
            100% { transform: translateY(0); }
        }

        @keyframes hologramEffect {
            0% { opacity: 1; }
            50% { opacity: 0.8; }
            100% { opacity: 1; }
        }

        @keyframes staticEffect {
            0% { transform: translate(0); }
            100% { transform: translate(1px, -1px); }
        }

        @keyframes filmGrain {
            0% { opacity: 0.9; }
            100% { opacity: 1; }
        }

        @keyframes kaleidoscopeEffect {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes technicolorShift {
            0% { filter: saturate(200%) hue-rotate(0deg); }
            100% { filter: saturate(200%) hue-rotate(360deg); }
        }

        @keyframes cctvScan {
            0% { filter: grayscale(100%) brightness(90%); }
            50% { filter: grayscale(100%) brightness(100%); }
            100% { filter: grayscale(100%) brightness(90%); }
        }

        @keyframes heatmapPulse {
            0% { filter: hue-rotate(180deg) saturate(200%); }
            50% { filter: hue-rotate(200deg) saturate(250%); }
            100% { filter: hue-rotate(180deg) saturate(200%); }
        }

        @keyframes timelapseEffect {
            0% { filter: brightness(100%) contrast(100%); }
            50% { filter: brightness(110%) contrast(120%); }
            100% { filter: brightness(100%) contrast(100%); }
        }

        .intro-outro-section {
            margin-top: 16px;
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .intro-outro-header {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .intro-outro-toggle {
            width: 20px;
            height: 20px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }

        .intro-outro-toggle.active {
            background: #2563eb;
        }

        .intro-outro-content {
            display: none;
        }

        .intro-outro-content.expanded {
            display: block;
        }

        .intro-outro-toggles {
            display: flex;
            gap: 16px;
            align-items: center;
            margin: 12px 0;
            justify-content: space-between;
            max-width: 200px;
        }

        .intro-outro-toggle-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .toggle-label {
            font-size: 12px;
            color: #666;
        }

        .intro-outro-drop {
            padding: 12px;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 12px;
            cursor: pointer;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .intro-outro-drop.filled {
            border-style: solid;
            border-color: #2563eb;
        }

        .intro-outro-drop .file-name {
            font-size: 12px;
            margin-top: 8px;
            word-break: break-all;
        }
        .controls .render-btn {
            margin-bottom: 12px;
        }

        .controls .render-btn:last-child {
            margin-bottom: 0;
        }
        
        .project-counter {
            font-size: 12px;
            color: #666;
            margin-left: 8px;
        }
        
        /* Update watermark and subtitle color picker styles */
        .color-picker-container {
            position: relative;
            display: inline-block;
            margin: 12px 0;
        }

        .color-picker-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #e9ecef;
            cursor: pointer;
            overflow: hidden;
        }

        .color-picker-input {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .subtitle-stroke-controls {
            margin: 12px 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .stroke-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stroke-width {
            width: 100%;
            padding: 8px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }
        .image-placeholders {
            position: relative;
        }
        .placeholder.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }
        .placeholder-drag-handle {
            position: absolute;
            left: 4px;
            top: 50%;
            transform: translateY(-50%);
            cursor: grab;
            padding: 4px;
            z-index: 4;
            opacity: 0.7;
            font-size: 16px;
        }
        .placeholder-drag-handle:hover {
            opacity: 1;
        }
        
        .sticker-group {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 16px;
        }

        .sticker-group .style-option {
            display: none;
        }

        .sticker-group .style-group-label:after {
            transform: rotate(-90deg);
        }

        .sticker-gallery {
            display: flex;
            gap: 8px;  
            padding: 12px 32px;
            overflow-x: scroll;
            scroll-behavior: smooth;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .sticker-gallery::-webkit-scrollbar {
            display: none;
        }

        .sticker-item {
            width: 50px;
            height: 50px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
        }

        .sticker-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sticker-timing-controls {
            display: flex;
            gap: 8px;
            padding: 8px 12px;
            justify-content: center;
        }

        .sticker-timing-btn {
            padding: 4px 8px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            cursor: pointer;
        }

        .sticker-timing-btn.active {
            background: #2563eb;
            color: white;
        }

        .preview-sticker {
            position: absolute;
            z-index: 1000;
            cursor: move;
        }

        .preview-sticker img {
            max-width: 100px;
        }

        .remove-sticker-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            border: none;
            z-index: 1001;
        }
        
        .sticker-gallery-container {
            position: relative;
            overflow: hidden;
        }

        .sticker-gallery-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1;
            border: 1px solid #e9ecef;
        }

        .sticker-gallery-nav:hover {
            background: rgba(255,255,255,0.9);
        }

        .sticker-gallery-prev {
            left: 4px;
        }

        .sticker-gallery-next { 
            right: 4px;
        }

        .sticker-group.collapsed .sticker-timing-controls {
            display: none;
        }

        .sticker-group.collapsed .sticker-gallery-container {
            display: none;
        }

        .sticky-preview {
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .sticky-preview-btn {
            padding: 8px 12px;
            font-size: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #f0f4f8 100%);
            border: 1px solid #c5d1e3;
            border-radius: 8px;
            color: #2563eb;
            font-weight: 600;
            box-shadow: 
                0 2px 4px rgba(37, 99, 235, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            margin-right: 8px;
        }

        .sticky-preview-btn.active {
            background: #2563eb;
            color: white;
        }

        .sticky-preview-btn:hover {
            background: linear-gradient(180deg, #f8faff 0%, #e8eef8 100%);
            transform: translateY(-1px);
            box-shadow: 
                0 4px 8px rgba(37, 99, 235, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        
        .audio-drop {
            position: relative;
        }
        .remove-audio-btn {
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            border: none;
            z-index: 3;
            padding: 0;
        }
        .remove-audio-btn:hover {
            background: rgba(255, 0, 0, 0.8);
            color: white;
        }
        /* Adjust randomize button position */
        .global-duration button.randomize-btn {
            margin-left: -35px;
        }
        .placeholder {
            position: relative;
        }
        .placeholder-audio {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.8);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            border: 2px dashed #e9ecef;
        }
        .placeholder-audio:hover {
            background: rgba(255,255,255,1);
        }
        .remove-sound-btn {
            position: absolute;
            top: 0;
            right: 0;
            width: 12px;
            height: 12px;
            background: rgba(255,0,0,0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 8px;
            cursor: pointer;
            z-index: 3;
        }
        .project-section {
            position: relative;
        }
        .remove-project-btn {
            position: absolute;
            top: 12px;
            left: -13px;
            background: rgba(255,0,0,0.8);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            border: none;
            z-index: 3;
            padding: 0;
            color: white;
        }
        .remove-project-btn:hover {
            background: rgba(255,0,0,0.8);
        }
    </style>
@endpush


@section('content')
<div class="container py-5">

    @foreach($news as $key => $new)
        <h2>{{$key}}</h2>
      <div class="list-group">
        @foreach($new as $n)
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <a href="{{route('detailPage',$n->slug)}}">
                    <div><span class="news-time">{{$n->time}}</span> - <span class="news-meta">{{$n->title}}</span></div>
                    <div class="d-none news-comments">💬 1</div>
                </a>
            </div>
        @endforeach
      </div>
    @endforeach
  <!-- Section 1 -->

</div>
@endsection
