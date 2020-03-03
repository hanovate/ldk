/**
 * DataRow.js
 *
 * @file A component for displaying a record encapsulated by <td> tag
 *
 * @version 0.1.0 2019-10-17 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React from "react";
import { FaEdit, FaTrash} from "react-icons/fa";

export default function DataRow(data) {
  const deleteformLink = _basePath + '/' + data.id + "/deleteform";
  const editformLink = _basePath + '/' + data.id + "/editform";

  return (
    <tr>
      { Object.entries(data).map(([key,datum],i) => <td key={i}>{datum}</td>) }
      <td>
        <div className="row">
          <div className="col text-left">
              <a href={editformLink}><FaEdit color="green" /></a>
          </div>
          <div className="col text-right">
            <a href={deleteformLink}><FaTrash /></a>
          </div>
        </div>
      </td>
    </tr>);
}
