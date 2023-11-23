<style>
    @font-face {
        font-family: SourceSansPro;
        src: url({{storage_path('fonts/SourceSansPro-Regular.ttf')}});
    }

    .clearfix:after {
        content: "";
        display: table;
        clear: both;
    }

    a,
    #client .to {
        color: #0087C3;
        text-decoration: none;
    }

    body {
        position: relative;
        width: 100%;
        height: 29.7cm;
        margin: 0 auto;
        color: #555555;
        background: #FFFFFF;
        font-size: 14px;
        font-family: "SourceSansPro";
        font-weight: normal;
    }

    header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
    }

    #logo {
        float: left;
        margin-top: 8px;
    }

    #logo img {
        height: 70px;
    }

    #company {
        float: right;
        text-align: right;
    }

    #details {
        margin-bottom: 50px;
    }

    #xclient {
        padding-right: 6px;
        border-right: 6px solid #0087C3;
        float: right;
    }

    #client {
        padding-left: 6px;
        border-left: 6px solid #0087C3;
        float: left;
    }

    h2.name {
        font-size: 1.4em;
        font-weight: normal;
        margin: 0;
    }

    #qrcode {
        position: absolute;
        text-align: center;
        width: 150px;
        height: 150px;
    }

    #qrcode img {
        object-fit: cover;
        padding: 5px;
        border: 3px solid #d946ef;
    }

    #invoice {
        float: right;
        text-align: right;
    }

    #invoice h1 {
        color: #0087C3;
        font-size: 2.4em;
        line-height: 1em;
        font-weight: normal;
        margin: 0 0 10px 0;
    }

    #invoice .date {
        font-size: 1.1em;
        color: #777777;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
    }

    table th,
    table td {
        padding-left: 3px;
        padding-right: 5px;
        padding-top: 5px;
        padding-bottom: 5px;
        background: #EEEEEE;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;
    }

    table th {
        white-space: nowrap;
        font-weight: normal;
    }
    th.table_header {
        transform: rotate(-90deg);
        position: absolute;
        left: -40px;
        bottom: 52%;
        font-size: 1em;
        font-weight: bold;
    }


    table td {
        text-align: right;
    }

    table td h3 {
        color: #d946ef;
        font-size: 1.2em;
        font-weight: normal;
        margin: 0 0 0.2em 0;
    }

    table .no {
        color: #FFFFFF;
        font-size: 1.6em;
        background: #d946ef;
    }

    table th.desc {
        text-align: left;
        padding-left: 5px;
    }

    table td.desc {
        text-align: left;
    }

    table th.center {
        text-align: center;
        font-size: 1em;
        padding-left: 2px;
        padding-right: 2px;
    }

    table td.center {
        text-align: center;
        font-size: 1.2em;
        padding-left: 2px;
        padding-right: 2px;
    }

    table .unit {
        background: #DDDDDD;
        padding-left: 2px;
        padding-right: 2px;
    }

    table .qty {
        background: #DDDDDD;
        text-align: center;
        padding-left: 3px;
        padding-right: 3px;
    }

    table .total {
        background: #d946ef;
        color: #FFFFFF;
        text-align: center;
    }

    table td.unit,
    table td.qty,
    table td.total {
        font-size: 1.2em;
    }

    table tbody tr:last-child td {
        border: none;
    }

    table tfoot td {
        padding: 10px 20px;
        background: #FFFFFF;
        border-bottom: none;
        font-size: 1.2em;
        white-space: nowrap;
        border-top: 1px solid #AAAAAA;
    }

    table tfoot tr:first-child td {
        border-top: none;
    }

    table tfoot tr:last-child td {
        color: #d946ef;
        font-size: 1.4em;
        border-top: 1px solid #d946ef;
    }

    table tfoot tr td:first-child {
        border: none;
    }

    #thanks {
        font-size: 2em;
        margin-bottom: 50px;
    }

    #notices {
        padding-left: 6px;
        border-left: 6px solid #0087C3;
    }

    #notices .notice {
        font-size: 1.2em;
    }

    footer {
        color: #777777;
        width: 100%;
        height: 30px;
        position: absolute;
        bottom: 0;
        border-top: 1px solid #AAAAAA;
        padding: 8px 0;
        text-align: center;
    }

    #table_header {
        transform: rotate(-90deg);
        position: absolute;
        left: -40px;
        bottom: 52%;
        font-size: 1em;
        font-weight: bold;
    }


    .bg-primary {
        background-color: #6e0486;
    }

    .bg-secondary {
        background-color: #4d4f4f;
    }

    .bg-muted {
        background-color: #888888;
    }

    .text-center {
        text-align: center;
    }

    /*CSS Utilities*/


    /* Display */
    .block {
        display: block;
    }

    .inline-block {
        display: inline-block;
    }

    .inline {
        display: inline;
    }

    .flex {
        display: flex;
    }

    .inline-flex {
        display: inline-flex;
    }

    /* Position */
    .static {
        position: static;
    }

    .fixed {
        position: fixed;
    }

    .absolute {
        position: absolute;
    }

    .relative {
        position: relative;
    }

    .sticky {
        position: sticky;
    }

    /* Visibility */
    .visible {
        visibility: visible;
    }

    .invisible {
        visibility: hidden;
    }

    /* Overflow */
    .overflow-auto {
        overflow: auto;
    }

    .overflow-hidden {
        overflow: hidden;
    }

    .overflow-visible {
        overflow: visible;
    }

    .overflow-scroll {
        overflow: scroll;
    }

    .overflow-x-auto {
        overflow-x: auto;
    }

    .overflow-y-auto {
        overflow-y: auto;
    }

    .overflow-x-hidden {
        overflow-x: hidden;
    }

    .overflow-y-hidden {
        overflow-y: hidden;
    }

    .overflow-x-visible {
        overflow-x: visible;
    }

    .overflow-y-visible {
        overflow-y: visible;
    }

    .overflow-x-scroll {
        overflow-x: scroll;
    }

    .overflow-y-scroll {
        overflow-y: scroll;
    }

    /* Text Alignment */
    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-justify {
        text-align: justify;
    }

    /* Text Color */
    .text-black {
        color: #000000;
    }

    .text-white {
        color: #ffffff;
    }

    /* Background Color */
    .bg-black {
        background-color: #000000;
    }

    .bg-white {
        background-color: #ffffff;
    }

    /* Font Weight */
    .font-normal {
        font-weight: normal;
    }

    .font-bold {
        font-weight: bold;
    }

    /* Font Size */
    .text-xs {
        font-size: 0.75rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-base {
        font-size: 1rem;
    }

    .text-lg {
        font-size: 1.125rem;
    }

    .text-xl {
        font-size: 1.25rem;
    }

    .text-2xl {
        font-size: 1.5rem;
    }

    /* Width */
    .w-full {
        width: 100%;
    }

    .w-screen {
        width: 100vw;
    }

    .w-auto {
        width: auto;
    }

    /* Height */
    .h-full {
        height: 100%;
    }

    .h-screen {
        height: 100vh;
    }

    .h-auto {
        height: auto;
    }

    /* Margin */
    .m-0 {
        margin: 0;
    }

    .m-1 {
        margin: 0.25rem;
    }

    .m-2 {
        margin: 0.5rem;
    }

    .m-3 {
        margin: 0.75rem;
    }

    .m-4 {
        margin: 1rem;
    }

    /* Padding */
    .p-0 {
        padding: 0;
    }

    .p-1 {
        padding: 0.25rem;
    }

    .p-2 {
        padding: 0.5rem;
    }

    .p-3 {
        padding: 0.75rem;
    }

    .p-4 {
        padding: 1rem;
    }

    /* Border */
    .border {
        border: 1px solid #000000;
    }

    .border-none {
        border: none;
    }

    /* Rounded Corners */
    .rounded-none {
        border-radius: 0;
    }

    .rounded-sm {
        border-radius: 0.125rem;
    }

    .rounded {
        border-radius: 0.25rem;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    /* Shadows */
    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .shadow {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /*Extra*/

    /* Justify Content */
    .justify-start {
        justify-content: flex-start;
    }

    .justify-center {
        justify-content: center;
    }

    .justify-end {
        justify-content: flex-end;
    }

    .justify-between {
        justify-content: space-between;
    }

    .justify-around {
        justify-content: space-around;
    }

    .justify-evenly {
        justify-content: space-evenly;
    }

    /* Align Items */
    .items-start {
        align-items: flex-start;
    }

    .items-center {
        align-items: center;
    }

    .items-end {
        align-items: flex-end;
    }

    .items-baseline {
        align-items: baseline;
    }

    .items-stretch {
        align-items: stretch;
    }

    /* Grid */
    .grid {
        display: grid;
    }

    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    /* Rows */
    .grid-rows-1 {
        grid-template-rows: repeat(1, minmax(0, 1fr));
    }

    .grid-rows-2 {
        grid-template-rows: repeat(2, minmax(0, 1fr));
    }

    .grid-rows-3 {
        grid-template-rows: repeat(3, minmax(0, 1fr));
    }

    .grid-rows-4 {
        grid-template-rows: repeat(4, minmax(0, 1fr));
    }

    /* Flex */
    .flex-1 {
        flex: 1;
    }

    .flex-auto {
        flex: auto;
    }

    .flex-initial {
        flex: initial;
    }

    .flex-none {
        flex: none;
    }


</style>
