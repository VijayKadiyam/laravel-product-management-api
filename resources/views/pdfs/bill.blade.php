<!-- Format Number -->
<?php 

  // $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

  // TO get the first letters
  preg_match_all('/\b\w/', $bill->company->name, $matches); 

?>

<!DOCTYPE html>

<html lang="en">

  <head>

    <title>{{ $settings->bill_format }}{{ $bill->bill_no }}</title>

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

  </head>

  <body> 

    <!-- Header -->
    <!-- <div class="header">
      <img src="" width="100%">
    </div> -->  

    <!-- Bill Heading -->
    <h3 align="center">TAX INVOICE</h3>

    <div class="wrapper">
      <table> 
        <!-- Company Name and Address-->
        <tr align="center">
          <td>
            <b>{{ $bill->company->name }}</b>
            <br>
            {{ $bill->company->address }}
            <br>
            <b>GSTIN/UIN: </b>{{ $bill->company->gstn_no }}
            <br>
            <b>State Code: </b> {{ $bill->company->state_code }}
            <br>
            <b>Email ID:</b> {{ $bill->company->email }}
            <br>
            <b>Mob No. </b> {{ $bill->company->contact1 }}
          </td> 
        </tr>   
      </table>
    </div>

    <!-- Basic Details -->
    <div class="wrapper"> 

      <div class="div-inline">

        <table> 

          <!-- Consignee Name and Address-->
          <tr>
            <td>
              Consignee
              <br>
              <b>{{ $bill->customer->name }}</b>
              <br>
              {{ $bill->customer->address }}
              <br>
              <b>GSTIN/UIN: </b>{{ $bill->customer->gst_no }}
              <br>
              <b>State Code: </b> {{ $bill->customer->state_code }}
              <br>
              <b>Email ID:</b> {{ $bill->customer->email }}
              <br>
              <b>Mob No. </b>{{ $bill->customer->contact1 }}
            </td> 
          </tr>  

          <!-- Buyer Name and Address-->
          <tr>
            <td>
              Buyer
              <br>
              <b>{{ $bill->customer->name }}</b>
              <br>
              {{ $bill->customer->address }}
              <br>
              <b>GSTIN/UIN: </b>{{ $bill->customer->gst_no }}
              <br>
              <b>State Code: </b> {{ $bill->customer->state_code }}
              <br>
              <b>Email ID:</b> {{ $bill->customer->email }}
              <br>
              <b>Mob No. </b>{{ $bill->customer->contact1 }}
            </td> 
          </tr>  
 
        </table>  
        
      </div>

      <div class="div-inline">

        <table>

          <!-- Invoice No -->
          <tr>
            <td>
              Invoice No
              <br>
              <b>{{ $settings->bill_format }}{{ $bill->bill_no }}</b>
            </td>
            <td>
              Invoice Date
              <br>
              <b>{{ Carbon\Carbon::parse($bill->created_at)->format('d-m-Y') }} </b>
            </td>
          </tr> 

          <tr>
            <td>
              Delivery Note
              <br>
              <b>{{ $bill->delivery_note }}</b>
            </td>
            <td>
              Delivery Note Date
              <br>
              <b>{{ $bill->delivery_note_date }}</b>
            </td>
          </tr>

          <tr>
            <td>
              Supplier Reference
              <br>
              <b>{{ $bill->supplier_reference }}</b>
            </td>
            <td>
              Terms of payment
              <br>
              <b>{{ $bill->terms_of_payment }}</b>
            </td>
          </tr>

          <tr>
            <td>
              Buyer order no
              <br>
              <b>{{ $bill->buyer_order_no }}</b>
            </td>
            <td>
              Destination
              <br>
              <b>{{ $bill->destination }}</b>
            </td>
          </tr>

          <tr>
            <td>
              Dispatch document no
              <br>
              <b>{{ $bill->despatch_document_no }}</b>
            </td>
            <td>
              Dispatch through
              <br>
              <b>{{ $bill->despatch_through }}</b>
            </td>
          </tr>

          <tr>
            <td colspan="2">
              Terms of delivery
              <br>
              <b>{{ $bill->terms_of_delivery }}</b>
            </td> 
          </tr>

        </table>
        
      </div>

    </div>  

    <!-- Charges Details -->
    <h4><u>Bill Description as follows:</u></h4>
    <div class="wrapper">

      <table>
        <!-- Heading   -->
        <tr align="center">
          <td>
            <b>Sr. No.</b>
          </td>
          <td colspan="3">
            <b>Description of Goods</b>
          </td>
          <td>
            <b>HSN Code</b>
          </td>
          <td>
            <b>Quantity</b>
          </td> 
          <td>
            <b>Rate per Bag</b>
          </td>
          <td>
            <b>Amount</b>
          </td>
        </tr>

        <!-- Billing details -->
        @foreach($bill->billing_details as $billing_detail)
        <tr align="center" class="noBottomBorder"> 

            <!-- Sr. No -->
            <td style="height:40px;" class="align-top">
              {{ $loop->index + 1 }}
            </td>

            <!-- Description -->
            <td colspan="3">
              {{ $billing_detail->product_category->name }}
            </td>

            <!-- HSN Code -->
            <td>
              {{ $billing_detail->product_category->hsn_code }}
            </td>

            <!-- Quantity -->
            <td>
              {{ $billing_detail->qty }} Bags
            </td>

            <!-- Cose per unit -->
            <td>
              Rs. {{ number_format($billing_detail->cost_per_unit) }}
            </td>

            <!-- Amount -->
            <td>
              Rs. {{ number_format($billing_detail->amount) }}
            </td>

        </tr>
        @endforeach  

        <!-- Sub total -->
        <tr style="border-top-color: black !important">
          
          <td>
          </td>
          <td colspan="3">
          </td> 

          <td></td>
          <td></td>

          <!-- Amount -->
          <td align="right">
            <b>Amount: </b>
          </td>
          <td align="center">
            Rs. {{ number_format( $bill->sub_total ) }}
          </td>

        </tr>

        <!-- GSTN Part -->
        <tr>
          
          <!-- GSTN No -->
          <td>
            <b>GSTN No.</b>
          </td>
          <td colspan="4">
            {{ $bill->company->gstn_no }}
          </td>

          <!-- SGST -->
          @foreach($bill->billing_taxes as $billing_tax)
          <td colspan="2" align="right">
            <b>SGST Tax @ {{ $billing_tax->tax->tax_percent/2 }}%</b>
          </td>
          <td align="center">
            @if($bill->customer->state_code == $bill->company->state_code)
              Rs. {{ number_format( $billing_tax->amount/2 ) }}
            @endif
          </td>
          @endforeach

        </tr>

        <!-- Pan Part -->
        <tr>
          
          <!-- PAN No -->
          <td>
            <b>PAN No.</b>
          </td>
          <td colspan="4">
            {{ $bill->company->pan_no }}
          </td>

          <!-- CGST -->
          @foreach($bill->billing_taxes as $billing_tax)
          <td colspan="2" align="right">
            <b>CGST Tax @ {{ $billing_tax->tax->tax_percent/2 }}%</b>
          </td>
          <td align="center">
            @if($bill->customer->state_code == $bill->company->state_code)
              Rs. {{ number_format( $billing_tax->amount/2 ) }}
            @endif
          </td>
          @endforeach

        </tr>

        <!-- Amount in words Part -->
        <tr>
          
          <!-- Amount in words -->
          <td rowspan="2">
            <b>Amount in words</b>
          </td>
          <td colspan="4" rowspan="2"> 
          </td>

          <!-- IGST -->
          @foreach($bill->billing_taxes as $billing_tax)
          <td colspan="2" align="right">
            <b>IGST Tax @ 18%</b>
          </td>
          <td align="center">
            @if($bill->customer->state_code != $bill->company->state_code)
              Rs. {{ number_format( $billing_tax->amount ) }}
            @endif
          </td>
          @endforeach

        </tr> 

        <tr>

          <!-- Grand Total -->
          <td colspan="2" align="right">
            <b>Grand Total</b>
          </td>
          <td align="center">
            <?php $total_tax = 0; ?>
            @foreach($bill->billing_taxes as $billing_tax)
              <?php $total_tax += $billing_tax->amount ?>
            @endforeach
            <?php $total = $bill->sub_total + $total_tax ?>
            Rs. {{ number_format( $total ) }}
          </td>

        </tr>


      </table>
      
    </div> 

    <!-- Account Details  -->
    <div class="wrapper">

      <table>
          
        <tr align="center">
          <td>
            <b>Account Details</b>
          </td>
          <td>
            <b>Name :</b>
            <br>
            {{ $bill->company->acc_name }}
          </td>
          <td>
            <b>A/C No. :</b>
            <br>
            {{ $bill->company->acc_no }}
          </td>
          <td>
            <b>IFSC Code :</b>
            <br>
            {{ $bill->company->ifsc }}
          </td>
          <td>
            <b>Branch :</b>
            <br>
            {{ $bill->company->branch }}
          </td>
        </tr>

      </table>
      
    </div> 

    <!-- Terms -->
    <div class="wrapper">

      <table>

        <tr>
          
          <td>

            <b>Terms and Conditions </b> 
            <br>

            1. Interest @ 18% p.a. will be charged if this payment is not paid within 15 days after submission.
            <br>
            2. Error and submission in this invoice shall be subject to the jurisdication of Panvel

          </td>

          <td style="vertical-align: top" align="center">
            <small>
              Certified that the particular given above are true and correct
            </small>
            <br>
            <b>For {{ strtoupper($bill->company->name) }}</b>
            <br>
            <br> 
            <br> 
            <label>
              <b>Authorized Signatory</b>
            </label>
          </td>

        </tr>  

      </table>
      
    </div> 
  
  </body>

</html>