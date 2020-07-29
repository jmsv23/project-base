/**
 * counter
 */
import React from 'react';
import ReactDOM from 'react-dom';
import Counter from 'lib/components/Counter';

// Module dependencies
import 'protons';

// Module template
import './_counter.twig';

export const name = 'counter';

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
// eslint-disable-next-line no-unused-vars
export function enable($context, { counter = {} }) {
  if (document.getElementById('counter')) {
    ReactDOM.render(<Counter />, document.getElementById('counter'));
  }
}

export default enable;
