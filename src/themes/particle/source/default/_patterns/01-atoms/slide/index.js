/**
 * slide
 */
import React from 'react';
import ReactDOM from 'react-dom';
// import $ from 'jquery';

// Module dependencies
import 'protons';

// Module template
import './_slide.twig';
import SimpleComponent from 'lib/components/SimpleComponent';
import './slide.scss';

export const name = 'slide';

/**
 * Components may need to run clean-up tasks if they are removed from DOM.
 *
 * @param {jQuery} $context - A piece of DOM
 * @param {Object} settings - Pertinent settings
 */
// eslint-disable-next-line no-unused-vars
export function disable($context, settings) {}

/**
 * Each component has a chance to run when its enable function is called. It is
 * given a piece of DOM ($context) and a settings object. We destructure our
 * component key off the settings object and provide an empty object fallback.
 * Incoming settings override default settings via Object.assign().
 *
 * @param {jQuery} $context - A piece of DOM
 * @param {Object} settings - Settings object
 */
export function enable() {
  if (document.getElementById('slide')) {
    ReactDOM.render(<SimpleComponent />, document.getElementById('slide'));
  }
}

export default enable;
