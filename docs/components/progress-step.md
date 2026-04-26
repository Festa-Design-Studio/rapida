# Progress-step atom — accessibility & copy contract

`<x-atoms.progress-step />` — multi-step progress indicator (used in the wizard).

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `current` | int | required | 1-indexed current step. |
| `total` | int | required | Total step count. |
| `variant` | string | `dots` | `dots` (filled circles) or `bar` (linear progress). |

## Keyboard contract

Static — informational, not interactive. Does not steal focus.

## ARIA contract

- Wraps in `<div role="progressbar" aria-valuemin="1" aria-valuemax="{total}" aria-valuenow="{current}" aria-label="Step {current} of {total}">`.
- For multi-step *navigation* (operators clicking back to step 3), use `<nav aria-label="Wizard steps">` with explicit links — that's a different molecule, not this atom.

## Focus management

N/A — purely informational.

## Screen reader behavior

- Announced as "Progress, step {current} of {total}".
- Updates on step change announce the new value.

## Copy contract

This atom has no text by default. Companion line below (in wizard-shell) reads "Step {current} of {total}" — match that wording exactly to avoid SR users hearing the same number twice in different formats.
