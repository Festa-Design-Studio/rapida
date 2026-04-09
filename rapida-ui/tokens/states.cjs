// RAPIDA UI — States (Composed)
// A user in crisis must always know the current state of any element.

const interaction = require('./states/interaction.cjs');
const validation  = require('./states/validation.cjs');
const data        = require('./states/data.cjs');
const damage      = require('./states/damage.cjs');
const motion      = require('./states/motion.cjs');

module.exports = {
  ...interaction,
  ...validation,
  ...data,
  ...damage,
  ...motion,
};
