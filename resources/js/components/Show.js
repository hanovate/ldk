/**
 * Show.js
 *
 * @file A show form render file for FormShow
 *
 * @version 0.1.0 2019-10-18 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React from 'react';
import { render } from "react-dom";
import DisplayRecord from "./DisplayRecord";

/*
const _data_attributes = {
  form: [
    {
      identifier: 'hs_code',
      label: 'high school code',
      type: 'number',
      attributes: {
        min: 0,
        max: 1000000,
        required: 'required'
      }
    },
    {
      identifier: 'district_code',
      label: 'district code',
      type: 'number',
      attributes: {
        min: 0,
        max: 1000,
        required: 'required'
      }
    },
    {
      identifier: 'district_name',
      label: 'district name',
      type: 'text',
      attributes: {
        required: 'required'
      }
    },
    {
      identifier: 'public_or_not',
      label: 'public or not',
      type: 'dropdown',
      values: ['P','N']
    }
  ]
};
*/

render(
  <DisplayRecord displayAttributes={_display_attributes} />
  , document.getElementById(_target_tag)
);
