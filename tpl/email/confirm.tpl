{include file="elib://email/header.tpl"}


        <p>{$body|replace:"\r\n":"</p><p>"|replace:"\r":"</p><p>"|replace:"\n":"</p><p>"}</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <table style="margin: 0; padding: 0; border: 0; display:  block; border-collapse: collapse; border-spacing: 0; width: 100%; display: table;">    
          <tr>
            <td style="border: 0px; width: 30%; background-color: #ffffff;"></td>
            <td style="border: 0px; text-align: center; width: 10%; background-color: #ffffff;">
              <div style="border-radius: 10px; border: 1px solid grey; padding: 10px;">
                {$reg[0]}
              </div>
            </td>
            <td style="border: 0px; text-align: center; width: 10%; background-color: #ffffff;">
              <div style="border-radius: 10px; border: 1px solid grey; padding: 10px;">
                {$reg[1]}
              </div>
            </td>
            <td style="border: 0px; text-align: center; width: 10%; background-color: #ffffff;">
              <div style="border-radius: 10px; border: 1px solid grey; padding: 10px;">
                {$reg[2]}
              </div>
            </td>
            <td style="border: 0px; ext-align: center; width: 10%; background-color: #ffffff;">
              <div style="border-radius: 10px; border: 1px solid grey; padding: 10px;">
                {$reg[3]}
              </div>
            </td>
            <td style="border: 0px; width: 30%; background-color: #ffffff;"></td>
          </tr>
        </table>
              
{include file="elib://email/footer.tpl"}

