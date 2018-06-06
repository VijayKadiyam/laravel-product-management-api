
<!-- Style-->
<style type="text/css">
  
  body {
    font-family: "Times New Roman", Georgia, Serif;  
    //padding-top: 70px;
    font-size: 14px;
  } 

  .header {
    top: 0px;
    position: fixed;
  }

  .footer {
    bottom: 0px;
    position: fixed;
  }

  table {
    width: 100%;
    table-layout: fixed;
  }

  td {
    padding: 2px;
  }

  tr, td {
    border: 1px solid black;
  }

  table {
    border: 1px solid black;
    border-collapse: collapse;
  }

  tr.noBottomBorder td {
    border-bottom-color: transparent;
  }

  tr.noTopBorder td {
    border-top-color: transparent;
  } 

  .wrapper {
    width: 100%;
    padding-bottom: 3px;
  }

  .div-inline {
    display: inline-block; 
    vertical-align: top;
    width: 49.5%; 
  }

  .align-top {
    vertical-align: top;
  }

  .align-bottom {
    vertical-align: bottom;
  }

</style>

<!-- Bill Heading -->
<h3 align="center">{{ $stock_category->name }} between {{ $fromDate->format('d-m-Y') }} - {{ $toDate->format('d-m-Y') }} (Opening Balance: {{ $opening_balance }} Kgs.)</h3>

<table>  
  <tr align="center">

  @foreach($keys as $key)
    <td><b>{{ $key }}</b></td>
  @endforeach
  </tr>
  @foreach($data as $row)
  <tr align="center"> 
    @foreach($row as $key => $value)
      <td>{{ $value }}</td>
    @endforeach
  </tr>
  @endforeach
</table>
