'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************

'START OF DECLARACTIONS
Dim I As Integer
Dim J As Integer
Dim F As Integer
Dim DataToPrint As String
Dim DataToEncode As String
Dim OnlyCorrectData As String
Dim PrintableString As String
Dim Encoding As String
Dim WeightedTotal As Long
Dim WeightValue As Integer
Dim CurrentValue As Long
Dim CheckDigitValue As Integer
Dim Factor As Integer
Dim CheckDigit As Integer
Dim CurrentEncoding As String
Dim NewLine As String
Dim msg As String
Dim CurrentChar As String
Dim CurrentCharNum As Integer
Dim C128_StartA As String
Dim C128_StartB As String
Dim C128_StartC As String
Dim C128_Stop As String
Dim C128Start As String
Dim C128CheckDigit As String
Dim StartCode As String
Dim StopCode As String
Dim Fnc1 As String
Dim LeadingDigit As Integer
Dim EAN2AddOn As String
Dim EAN5AddOn As String
Dim EANAddOnToPrint As String
Dim HumanReadableText As String
Dim StringLength As Integer
Dim CorrectFNC As Integer
'END OF DECLARACTIONS


Public Function Code128(DataToFormat As String, ReturnType As Integer, ApplyTilde as Boolean) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    CorrectFNC = 0
    PrintableString = ""
    
    'Additional logic needed in case ReturnType is not correct
    If ReturnType < 0 Or ReturnType > 5 Then ReturnType = 0
    
    '2006.2 BDA moved code to the ProcessTilde function
    If ApplyTilde Then DataToFormat = ProcessTilde(DataToFormat)
    
If ReturnType = 0 Or ReturnType = 2 Then
    'ReturnType 0 = format the data to the font
    'Select the character set A, B or C for the START character
    CurrentChar = Left(DataToFormat, 1)
    CurrentCharNum = Asc(CurrentChar)
    If CurrentCharNum < 32 Then C128Start = Chr(203)
    If CurrentCharNum > 31 And CurrentCharNum < 127 Then C128Start = Chr(204)
    If ((StringLength > 3) And IsNumeric(Mid(DataToFormat, 1, 4))) Then C128Start = Chr(205)
   
    '202 & 212-215 is for the FNC1, with this Start C is mandatory
    If CurrentCharNum = 197 Then C128Start = Chr(204)
    If CurrentCharNum > 201 Then C128Start = Chr(205)
    If C128Start = Chr(203) Then CurrentEncoding = "A"
    If C128Start = Chr(204) Then CurrentEncoding = "B"
    If C128Start = Chr(205) Then CurrentEncoding = "C"
    StringLength = Len(DataToFormat)
    For I = 1 To StringLength
        'Check for FNC1 in any set which is ASCII 202 and ASCII 212-215
        CurrentCharNum = Asc(Mid(DataToFormat, I, 1))       
        If CurrentCharNum > 201 Then
            DataToEncode = DataToEncode & Chr(202)
        'Check for switching to character set C
        ElseIf CurrentCharNum = 197 Then  
            If CurrentEncoding = "C" Then 
                DataToEncode = DataToEncode & Chr(200)
                CurrentEncoding = "B"
            End If
            DataToEncode = DataToEncode & Chr(197)
        ElseIf ((I < StringLength - 2) And (IsNumeric(Mid(DataToFormat, I, 1))) And (IsNumeric(Mid(DataToFormat, I + 1, 1))) And (IsNumeric(Mid(DataToFormat, I, 4)))) Or ((I < StringLength) And (IsNumeric(Mid(DataToFormat, I, 1))) And (IsNumeric(Mid(DataToFormat, I + 1, 1))) And (CurrentEncoding = "C")) Then
        
            '2005.12 BDA added this section 
            'check to see if we have an odd number of numbers to encode,
            'if so stay in current set for 1 number and then switch to save space
            If CurrentEncoding <> "C" Then
                J = I
                Factor = 3
                Do While J <= StringLength And IsNumeric(Mid(DataToFormat, J, 1))
                    Factor = 4 - Factor
                    J = J + 1
                Loop
                If Factor = 1 Then
                    'if so stay in current set for 1 character to save space
                    DataToEncode = DataToEncode & Chr(CurrentCharNum)
                    I = I + 1
                End If
            End If
            
            'Switch to set C if not already in it
            If CurrentEncoding <> "C" Then DataToEncode = DataToEncode & Chr(199)
            CurrentEncoding = "C"
            CurrentChar = (Mid(DataToFormat, I, 2))
            CurrentValue = CInt(CurrentChar)
            'Set the CurrentValue to the number of String CurrentChar
            If (CurrentValue < 95 And CurrentValue > 0) Then DataToEncode = DataToEncode & Chr(CurrentValue + 32)
            If CurrentValue > 94 Then DataToEncode = DataToEncode & Chr(CurrentValue + 100)
            If CurrentValue = 0 Then DataToEncode = DataToEncode & Chr(194)
            I = I + 1
        'Check for switching to character set A
        ElseIf (I <= StringLength) And ((Asc(Mid(DataToFormat, I, 1)) < 31) Or ((CurrentEncoding = "A") And (Asc(Mid(DataToFormat, I, 1)) > 32 And (Asc(Mid(DataToFormat, I, 1))) < 96))) Then
        'Switch to set A if not already in it
            If CurrentEncoding <> "A" Then DataToEncode = DataToEncode & Chr(201)
            CurrentEncoding = "A"
            'Get the ASCII value of the next character
            CurrentCharNum = Asc(Mid(DataToFormat, I, 1))
            If CurrentCharNum = 32 Then
                DataToEncode = DataToEncode & Chr(194)
            ElseIf CurrentCharNum < 32 Then
                DataToEncode = DataToEncode & Chr(CurrentCharNum + 96)
            ElseIf CurrentCharNum > 32 Then
                DataToEncode = DataToEncode & Chr(CurrentCharNum)
            End If
        'Check for switching to character set B
        ElseIf (I <= StringLength) And ((Asc(Mid(DataToFormat, I, 1))) > 31 And (Asc(Mid(DataToFormat, I, 1)))) < 127 Then
        'Switch to set B if not already in it
            If CurrentEncoding <> "B" Then DataToEncode = DataToEncode & Chr(200)
            CurrentEncoding = "B"
        'Get the ASCII value of the next character
            CurrentCharNum = Asc(Mid(DataToFormat, I, 1))
            If CurrentCharNum = 32 Then
                DataToEncode = DataToEncode & Chr(194)
            Else
                DataToEncode = DataToEncode & Chr(CurrentCharNum)
            End If
        End If
    Next I
End If

'FORMAT TEXT FOR AIs
If ReturnType = 1 Then
    'ReturnType 1 = format the data for human readable text only
    HumanReadableText = ""
    StringLength = Len(DataToFormat)
    For I = 1 To StringLength
        CorrectFNC = 0
        'Get ASCII value of each character
        CurrentCharNum = Asc(Mid(DataToFormat, I, 1))
        'Check for FNC1
        If ((I < StringLength - 2) And ((CurrentCharNum = 202) Or ((CurrentCharNum > 211) And (CurrentCharNum < 219)))) Then
        '2005.12 BDA updated the next if/else to eliminate errors from text after the AI
        'It appears that there is an AI
        'Get the value of the next 2 digits to try to determine the length of the AI, if text is used here
        'Set this value to 81 for a 4 digit AI
        If IsNumeric(Mid(DataToFormat, I + 1, 1)) And IsNumeric(Mid(DataToFormat, I + 2, 1)) Then
            CurrentChar = Mid(DataToFormat, I + 1, 2)
            CurrentCharNum = CInt(CurrentChar)
        Else
            CurrentCharNum = 81
        End If
        'Is 2 digit AI by entering ASCII 212?
            If ((CorrectFNC = 0) And (Asc(Mid(DataToFormat, I, 1)) = 212)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 2)) & ") "
                I = I + 2
                CorrectFNC = 1
        'Is 3 digit AI by entering ASCII 213?
            ElseIf ((I < StringLength - 3) And (CorrectFNC = 0) And (Asc(Mid(DataToFormat, I, 1)) = 213)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 3)) & ") "
                I = I + 3
                CorrectFNC = 1
        'Is 4 digit AI by entering ASCII 214?
            ElseIf ((I < StringLength - 4) And (CorrectFNC = 0) And (Asc(Mid(DataToFormat, I, 1)) = 214)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 4)) & ") "
                I = I + 4
                CorrectFNC = 1
        'Is 5 digit AI by entering ASCII 215?
            ElseIf ((I < StringLength - 5) And (CorrectFNC = 0) And (Asc(Mid(DataToFormat, I, 1)) = 215)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 5)) & ") "
                I = I + 5
                CorrectFNC = 1
        'Is 6 digit AI by entering ASCII 216?
            ElseIf ((I < StringLength - 6) And (CorrectFNC = 0) And (Asc(Mid(DataToFormat, I, 1)) = 216)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 6)) & ") "
                I = I + 6
                CorrectFNC = 1
        'Is 7 digit AI by entering ASCII 217?
            ElseIf ((I < StringLength - 7) And (CorrectFNC = 0) And (Asc(Mid(DataToFormat, I, 1)) = 217)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 7)) & ") "
                I = I + 7
                CorrectFNC = 1
        'Is 8 digit AI by entering ASCII 218?
            ElseIf ((I < StringLength - 8) And (CorrectFNC = 0) And (Asc(Mid(DataToFormat, I, 1)) = 218)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 8)) & ") "
                I = I + 8
                CorrectFNC = 1
        'Is 4 digit AI by detection?
            ElseIf ((I < StringLength - 4) And (CorrectFNC = 0) And ((CurrentCharNum <= 81 And CurrentCharNum >= 80) Or (CurrentCharNum <= 34 And CurrentCharNum >= 31))) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 4)) & ") "
                I = I + 4
                CorrectFNC = 1
        'Is 3 digit AI by detection?
            ElseIf ((I < StringLength - 3) And (CorrectFNC = 0) And ((CurrentCharNum <= 49 And CurrentCharNum >= 40) Or (CurrentCharNum <= 25 And CurrentCharNum >= 23))) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 3)) & ") "
                I = I + 3
                CorrectFNC = 1
        'Is 2 digit AI by detection?
            ElseIf ((CurrentCharNum <= 30 And (CorrectFNC = 0) And CurrentCharNum >= 0) Or (CurrentCharNum <= 99 And CurrentCharNum >= 90)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 2)) & ") "
                I = I + 2
                CorrectFNC = 1
        'If no AI was detected, set default to 4 digit AI:
            ElseIf ((I < StringLength - 4) And (CorrectFNC = 0)) Then
                HumanReadableText = HumanReadableText & " (" & (Mid(DataToFormat, I + 1, 4)) & ") "
                I = I + 4
                CorrectFNC = 1
            End If
        ElseIf (Asc(Mid(DataToFormat, I, 1)) < 32) Then
            HumanReadableText = HumanReadableText & " "
        ElseIf ((Asc(Mid(DataToFormat, I, 1)) > 31) And (Asc(Mid(DataToFormat, I, 1)) < 128)) Then
            HumanReadableText = HumanReadableText & Mid(DataToFormat, I, 1)
        End If
    Next I
End If

If ReturnType > 2 Then
    'ReturnType 3, 4 or 5 = format the data for human readable text only
    'inserting a space for every 3, 4 or 5 characters
    HumanReadableText = ""
    StringLength = Len(DataToFormat)
    J = 0
    For I = 1 To StringLength
        CurrentCharNum = Asc(Mid(DataToFormat, I, 1))
        If CurrentCharNum > 31 And CurrentCharNum < 128 Then
            HumanReadableText = HumanReadableText & Mid(DataToFormat, I, 1)
            J = J + 1
        End If
        If (J Mod ReturnType) = 0 Then HumanReadableText = HumanReadableText & " "
    Next I
End If

If ReturnType = 0 Or ReturnType = 2 Then
    '2006.2 BDA added the if block here for compatibility with different office versions
    DataToFormat = ""
    'Calculate Modulo 103 Check Digit
    WeightedTotal = Asc(C128Start) - 100
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
        CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
        If CurrentCharNum < 135 Then CurrentValue = CurrentCharNum - 32
        If CurrentCharNum > 134 Then CurrentValue = CurrentCharNum - 100
        If CurrentCharNum = 194 Then CurrentValue = 0
        CurrentValue = CurrentValue * I
        WeightedTotal = WeightedTotal + CurrentValue
        If CurrentCharNum = 32 Then CurrentCharNum = 194
        PrintableString = PrintableString & Chr(CurrentCharNum)
    Next I
    CheckDigitValue = (WeightedTotal Mod 103)
    If CheckDigitValue < 95 And CheckDigitValue > 0 Then C128CheckDigit = Chr(CheckDigitValue + 32)
    If CheckDigitValue > 94 Then C128CheckDigit = Chr(CheckDigitValue + 100)
    If CheckDigitValue = 0 Then C128CheckDigit = Chr(194)
End If
    
    DataToEncode = ""
    'ReturnType 0 returns data formatted to the barcode font
    If ReturnType = 0 Then Code128 = C128Start & PrintableString & C128CheckDigit & Chr(206)
    'ReturnType 1 returns data formatted for human readable text
    If ReturnType = 1 Or ReturnType > 2 Then Code128 = HumanReadableText
    'ReturnType 2 returns the check digit for the data supplied
    If ReturnType = 2 Then Code128 = C128CheckDigit
End Function


Public Function Code128a(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
     PrintableString = ""
     WeightedTotal = 103
     PrintableString = Chr(203)
     StringLength = Len(DataToEncode)
     For I = 1 To StringLength
          CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
          If CurrentCharNum < 135 Then CurrentValue = CurrentCharNum - 32
          If CurrentCharNum > 134 Then CurrentValue = CurrentCharNum - 100
          CurrentValue = CurrentValue * I
          WeightedTotal = WeightedTotal + CurrentValue
          If CurrentCharNum = 32 Then CurrentCharNum = 194
          PrintableString = PrintableString & Chr(CurrentCharNum)
     Next I
     CheckDigitValue = (WeightedTotal Mod 103)
     If CheckDigitValue < 95 And CheckDigitValue > 0 Then C128CheckDigit = Chr(CheckDigitValue + 32)
     If CheckDigitValue > 94 Then C128CheckDigit = Chr(CheckDigitValue + 100)
     If CheckDigitValue = 0 Then C128CheckDigit = Chr(194)
     PrintableString = PrintableString & C128CheckDigit & Chr(206)
     Code128a = PrintableString
End Function



Public Function Code128b(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
     PrintableString = ""
     WeightedTotal = 104
     PrintableString = Chr(204)
     StringLength = Len(DataToEncode)
     For I = 1 To StringLength
          CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
          If CurrentCharNum < 135 Then CurrentValue = CurrentCharNum - 32
          If CurrentCharNum > 134 Then CurrentValue = CurrentCharNum - 100
          CurrentValue = CurrentValue * I
          WeightedTotal = WeightedTotal + CurrentValue
          If CurrentCharNum = 32 Then CurrentCharNum = 194
          PrintableString = PrintableString & Chr(CurrentCharNum)
     Next I
     CheckDigitValue = (WeightedTotal Mod 103)
     If CheckDigitValue < 95 And CheckDigitValue > 0 Then C128CheckDigit = Chr(CheckDigitValue + 32)
     If CheckDigitValue > 94 Then C128CheckDigit = Chr(CheckDigitValue + 100)
     If CheckDigitValue = 0 Then C128CheckDigit = Chr(194)
     PrintableString = PrintableString & C128CheckDigit & Chr(206)
     Code128b = PrintableString
End Function


Public Function Code128c(DataToEncode As String, ReturnType As Integer) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    'Additional logic needed in case ReturnType is not entered
     If ReturnType <> 0 And ReturnType <> 1 And ReturnType <> 2 Then ReturnType = 0
     PrintableString = ""
     OnlyCorrectData = ""
     StringLength = Len(DataToEncode)
     For I = 1 To StringLength
          If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
     Next I
     DataToEncode = OnlyCorrectData
     If (Len(DataToEncode) Mod 2) = 1 Then DataToEncode = "0" & DataToEncode
     PrintableString = Chr(205)
     WeightedTotal = 105
     WeightValue = 1
     StringLength = Len(DataToEncode)
     For I = 1 To StringLength Step 2
          CurrentValue = CInt(Mid(DataToEncode, I, 2))
          If CurrentValue < 95 And CurrentValue > 0 Then PrintableString = PrintableString & Chr(CurrentValue + 32)
          If CurrentValue > 94 Then PrintableString = PrintableString & Chr(CurrentValue + 100)
          If CurrentValue = 0 Then PrintableString = PrintableString & Chr(194)
          CurrentValue = CurrentValue * WeightValue
          WeightedTotal = WeightedTotal + CurrentValue
          WeightValue = WeightValue + 1
     Next I
     CheckDigitValue = (WeightedTotal Mod 103)
     If CheckDigitValue < 95 And CheckDigitValue > 0 Then C128CheckDigit = Chr(CheckDigitValue + 32)
     If CheckDigitValue > 94 Then C128CheckDigit = Chr(CheckDigitValue + 100)
     If CheckDigitValue = 0 Then C128CheckDigit = Chr(194)
     If ReturnType = 0 Then Code128c = PrintableString & C128CheckDigit & Chr(206)
     If ReturnType = 1 Then Code128c = DataToEncode & CheckDigitValue
     If ReturnType = 2 Then Code128c = Str(CheckDigitValue)
End Function


Public Function I2of5(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************

    DataToPrint = ""
    DataToEncode = RTrim(LTrim(DataToEncode))
    OnlyCorrectData = ""
    ' Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
    'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    DataToEncode = OnlyCorrectData
    'Check for an even number of digits, add 0 if not even
    If (Len(DataToEncode) Mod 2) = 1 Then DataToEncode = "0" & DataToEncode
    'Assign start and stop codes
    StartCode = Chr(203)
    StopCode = Chr(204)
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength Step 2
        'Get the value of each number pair
        CurrentCharNum = Val((Mid(DataToEncode, I, 2)))
        'Get the ASCII value of CurrentChar
        If CurrentCharNum < 94 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 33)
        If CurrentCharNum > 93 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 103)
    Next I
    'Get Printable String
    PrintableString = StartCode + DataToPrint + StopCode
    'Return PrintableString
    I2of5 = PrintableString
End Function


Public Function Code39Mod43(DataToEncode As String, ReturnType As Integer) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    'Additional logic needed in case ReturnType is not correct
    If ReturnType <> 0 And ReturnType <> 1 And ReturnType <> 2 Then ReturnType = 0
    DataToEncode = UCase(DataToEncode)
    DataToPrint = ""
    OnlyCorrectData = ""
    'Only pass correct data
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
        'Get each character one at a time
        CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
        'Get the value of CurrentChar according to MOD43
        '0-9
        If CurrentCharNum < 58 And CurrentCharNum > 47 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        'A-Z
        If CurrentCharNum < 91 And CurrentCharNum > 64 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        'Space
        If CurrentCharNum = 32 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        '-
        If CurrentCharNum = 45 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        '.
        If CurrentCharNum = 46 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        '$
        If CurrentCharNum = 36 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        '/
        If CurrentCharNum = 47 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        '+
        If CurrentCharNum = 43 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
        '%
        If CurrentCharNum = 37 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I
    DataToEncode = OnlyCorrectData
    WeightedTotal = 0
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
        'Get each character one at a time
        CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
        'Get the value of CurrentChar according to MOD43
        '0-9
        If CurrentCharNum < 58 And CurrentCharNum > 47 Then CurrentValue = CurrentCharNum - 48
        'A-Z
        If CurrentCharNum < 91 And CurrentCharNum > 64 Then CurrentValue = CurrentCharNum - 55
        'Space
        If CurrentCharNum = 32 Then CurrentValue = 38
        '-
        If CurrentCharNum = 45 Then CurrentValue = 36
        '.
        If CurrentCharNum = 46 Then CurrentValue = 37
        '$
        If CurrentCharNum = 36 Then CurrentValue = 39
        '/
        If CurrentCharNum = 47 Then CurrentValue = 40
        '+
        If CurrentCharNum = 43 Then CurrentValue = 41
        '%
        If CurrentCharNum = 37 Then CurrentValue = 42
        'To print the barcode symbol representing a space it is necessary
        'to type or print "=" (the equal character) instead of a space character.
        If CurrentCharNum = 32 Then CurrentCharNum = 61
        'Gather data to print
        DataToPrint = DataToPrint & Chr(CurrentCharNum)
        'Add the values together
        WeightedTotal = WeightedTotal + CurrentValue
    Next I
    'Divide the WeightedTotal by 43 and get the remainder, this is the CheckDigit
    CheckDigitValue = (WeightedTotal Mod 43)
    'Assign values to characters
    '0-9
    If CheckDigitValue < 10 Then CheckDigit = CheckDigitValue + 48
    'A-Z
    If CheckDigitValue < 36 And CheckDigitValue > 9 Then CheckDigit = CheckDigitValue + 55
    'Space
    If CheckDigitValue = 38 Then CheckDigit = 61
    '-
    If CheckDigitValue = 36 Then CheckDigit = 45
    '.
    If CheckDigitValue = 37 Then CheckDigit = 46
    '$
    If CheckDigitValue = 39 Then CheckDigit = 36
    '/
    If CheckDigitValue = 40 Then CheckDigit = 47
    '+
    If CheckDigitValue = 41 Then CheckDigit = 43
    '%
    If CheckDigitValue = 42 Then CheckDigit = 37
    'ReturnType 0 returns data formatted to the barcode font
    If ReturnType = 0 Then Code39Mod43 = "!" & DataToPrint & Chr(CheckDigit) & "!" & " "
    'ReturnType 1 returns data formatted for human readable text
    If ReturnType = 1 Then Code39Mod43 = DataToPrint & Chr(CheckDigit)
    'ReturnType 2 returns the  check digit for the data supplied
    If ReturnType = 2 Then Code39Mod43 = Chr(CheckDigit)
End Function


Public Function Code39(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    DataToEncode = RTrim(LTrim(DataToEncode))
    'Check for spaces in code
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
        'Get each character one at a time
        CurrentChar = (Mid(DataToEncode, I, 1))
        'To print the barcode symbol representing a space it is necessary
        'to type or print "=" (the equal character) instead of a space character.
        If CurrentChar = " " Then CurrentChar = "="
        DataToPrint = DataToPrint & CurrentChar
    Next I
    'Get Printable String
    Code39 = "!" & DataToPrint & "!"
End Function


Public Function I2of5Mod10(DataToEncode As String, ReturnType As Integer) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    'Additional logic needed in case ReturnType is not entered
    If ReturnType <> 0 And ReturnType <> 1 And ReturnType <> 2 Then ReturnType = 0
    DataToPrint = ""
    OnlyCorrectData = ""
    'Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
    'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    DataToEncode = OnlyCorrectData
    'Calculate Check Digit
    Factor = 3
    WeightedTotal = 0
    For I = Len(DataToEncode) To 1 Step -1
        'Get the value of each number starting at the end
        CurrentCharNum = Mid(DataToEncode, I, 1)
        'Multiply by the weighting factor which is 3,1,3,1...
        'and add the sum together
        WeightedTotal = WeightedTotal + CurrentCharNum * Factor
        'Change factor for next calculation
        Factor = 4 - Factor
    Next I
    'Find the CheckDigit by finding the smallest number that = a multiple of 10
    I = (WeightedTotal Mod 10)
    If I <> 0 Then
         CheckDigit = (10 - I)
    Else
         CheckDigit = 0
    End If
    'Add check digit
    DataToEncode = DataToEncode & CheckDigit
    'Check for an even number of digits, add 0 if not even
    If (Len(DataToEncode) Mod 2) = 1 Then DataToEncode = "0" & DataToEncode
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength Step 2
        'Get the value of each number pair
        CurrentCharNum = (Mid(DataToEncode, I, 2))
        'Get the ASCII value of CurrentChar
        If CurrentCharNum < 94 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 33)
        If CurrentCharNum > 93 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 103)
    Next I
    'ReturnType 0 returns data formatted to the barcode font
    If ReturnType = 0 Then I2of5Mod10 = Chr(203) & DataToPrint & Chr(204)
    'ReturnType 1 returns data formatted for human readable text
    If ReturnType = 1 Then I2of5Mod10 = DataToEncode
    'ReturnType 2 returns the check digit for the data supplied
    If ReturnType = 2 Then I2of5Mod10 = Str$(CheckDigit)
End Function


Public Function MSI(DataToEncode As String, ReturnType As Integer) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    'Additional logic needed in case ReturnType is not entered correctly
    If ReturnType <> 0 And ReturnType <> 1 And ReturnType <> 2 Then ReturnType = 0
    'The MSI encoding function will only accept digits. Any non-numeric characters
    'will be discarded
    Dim DataToPrint As String       'output for function
    Dim OnlyCorrectData As String   'Only numeric characters pulled from DataToEncode
    Dim StringLength As Long        'Length of string
    Dim Idx As Integer              'for loop counter
    Dim OddNumbers As String        'String of odd position numbers used to create check digit
    Dim EvenNumberSum As Long       'all of the even position numbers added up
    Dim OddNumberProduct As Long    'Product of OddNumbers variable
    Dim sOddNumberProduct As String 'String version of OddNumberProduct variable
    Dim OddNumberSum As Long        'Sum of individual digits in sOddNumberProduct
    Dim OddDigit As Boolean         'Used to determine even/odd position digits.
    Dim CheckDigit As String        'This is the CheckDigit
    DataToPrint = ""
    OnlyCorrectData = ""
    'Check to make sure data is numeric
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
    'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    DataToEncode = OnlyCorrectData
    '<<<< Calculate Check Digit >>>>
    'To create the check digit follow these steps
    '1)Starting from the units position, create a new number with all of the odd
    '  position digits in their original sequence.
    '2)Multiply this new number by 2.
    '3)Add all of the digits of the product from step two.
    '4)Add all of the digits not used in step one to the result in step three.
    '5)Determine the smallest number which when added to the result in step four
    '  will result in a multiple of 10. This is the check character.
    'Step 1 -- Create a new number of the odd position digits starting from the right and going left, but store the
    'digits from left to right.
    'We will create the odd position number & prepare for Step 4 by getting the sum of all even position charactesr
    StringLength = Len(DataToEncode)
    OddNumbers = ""
    OddDigit = True
    EvenNumberSum = 0
    For Idx = StringLength To 1 Step -1
        If OddDigit = True Then
            OddNumbers = Mid(DataToEncode, Idx, 1) & OddNumbers
            OddDigit = False
        Else
            EvenNumberSum = EvenNumberSum + Val(Mid(DataToEncode, Idx, 1))
            OddDigit = True
        End If
    Next Idx
    'Step 2 -- Multiply this new number by 2.
    OddNumberProduct = Val(OddNumbers) * 2
    'Step 3 -- Add all of the digits of the product from step two.
    sOddNumberProduct = Format(OddNumberProduct)
    StringLength = Len(sOddNumberProduct)
    OddNumberSum = 0
    For Idx = 1 To StringLength
        OddNumberSum = OddNumberSum + Val(Mid(sOddNumberProduct, Idx, 1))
    Next Idx
    'Step 4 -- Add all of the digits not used in step one to the result in step three.
    'We will store the result in OddNumberSum just so we don't have to create another variable
    OddNumberSum = OddNumberSum + EvenNumberSum
    'Step 5 -- Determine the smallest number which when added to the result in step four
    'will result in a multiple of 10. This is the check character.
    OddNumberSum = OddNumberSum Mod 10
    If OddNumberSum <> 0 Then
        CheckDigit = Format(10 - OddNumberSum)
    Else
        CheckDigit = "0"
    End If
    Select Case ReturnType
       Case 0  'Returns formatted data for barcode
           DataToPrint = "(" & DataToEncode & CheckDigit & ")"
       Case 1  'Returns data formatted for human readable text.
               'Which means all of the invalid characters are
               'stripped out.
           DataToPrint = DataToEncode
       Case 2  'Returns just the check digit
           DataToPrint = CheckDigit
    End Select
    MSI = DataToPrint
End Function


Public Function UPCa(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    DataToPrint = ""
    OnlyCorrectData = ""
    'Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
    'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    '2006.2 BDA added the next line for general compatibility
    StringLength = Len(OnlyCorrectData)    
    'Remove check digits if they added one
    If StringLength < 11 Then OnlyCorrectData = "00000000000"
    If StringLength = 15 Then OnlyCorrectData = "00000000000"
    If StringLength > 18 Then OnlyCorrectData = "00000000000"
    If StringLength = 12 Then OnlyCorrectData = Mid(OnlyCorrectData, 1, 11)
    If StringLength = 14 Then OnlyCorrectData = Mid(OnlyCorrectData, 1, 11) & Mid(OnlyCorrectData, 13, 2)
    If StringLength = 17 Then OnlyCorrectData = Mid(OnlyCorrectData, 1, 11) & Mid(OnlyCorrectData, 13, 5)
    EAN2AddOn = ""
    EAN5AddOn = ""
    EANAddOnToPrint = ""
    '2006.2 BDA added the next line for general compatibility
    StringLength = Len(OnlyCorrectData)       
    If StringLength = 16 Then EAN5AddOn = Mid(OnlyCorrectData, 12, 5)
    If StringLength = 13 Then EAN2AddOn = Mid(OnlyCorrectData, 12, 2)
    'split 12 digit number from add-on
    
    DataToEncode = Mid(OnlyCorrectData, 1, 11)
    '<<<< Calculate Check Digit >>>>
    Factor = 3
    WeightedTotal = 0
    For I = Len(DataToEncode) To 1 Step -1
         'Get the value of each number starting at the end
         CurrentCharNum = Mid(DataToEncode, I, 1)
         'multiply by the weighting factor which is 3,1,3,1...
         'and add the sum together
         WeightedTotal = WeightedTotal + CurrentCharNum * Factor
         'change factor for next calculation
         Factor = 4 - Factor
    Next I
    'Find the CheckDigit by finding the number + WeightedTotal that = a multiple of 10
    'Divide by 10, get the remainder and subtract from 10
    I = (WeightedTotal Mod 10)
    If I <> 0 Then
         CheckDigit = (10 - I)
    Else
         CheckDigit = 0
    End If
    DataToEncode = DataToEncode & CheckDigit
    'Now that have the total number including the check digit, determine character to print
    'for proper barcoding
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
        'Get the ASCII value of each number
         CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
        'Print different barcodes according to the location of the CurrentChar
         Select Case I
         Case 1
            'For the first character, print the human readable character, the normal
            'guard pattern, and then the barcode without the human readable character
            '2006.2 BDA modified the next 2 lines for general compatibility
              If (CurrentCharNum-48) > 4 Then DataToPrint = Chr(CurrentCharNum + 64) & "(" & Chr(CurrentCharNum + 49)
              If (CurrentCharNum-48) < 5 Then DataToPrint = Chr(CurrentCharNum + 37) & "(" & Chr(CurrentCharNum + 49)
         Case 2
              DataToPrint = DataToPrint & Chr(CurrentCharNum)
         Case 3
              DataToPrint = DataToPrint & Chr(CurrentCharNum)
         Case 4
              DataToPrint = DataToPrint & Chr(CurrentCharNum)
         Case 5
              DataToPrint = DataToPrint & Chr(CurrentCharNum)
         Case 6
            'Print the center guard pattern after the 6th character
              DataToPrint = DataToPrint & Chr(CurrentCharNum) & "*"
         Case 7
            'Add 27 to the ASII value of characters 6-12 to print from character set C
            'This is required when printing to the right of the center guard pattern
              DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
         Case 8
              DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
         Case 9
              DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
         Case 10
              DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
         Case 11
              DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
         Case 12
            'For the last character, print the barcode without the human readable character,
            'the normal guard pattern, and then the human readable character.
            '2006.2 BDA modified the next 2 lines for general compatibility            
              If (CurrentCharNum-48) > 4 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 59) & "(" & Chr(CurrentCharNum + 64)
              If (CurrentCharNum-48) < 5 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 59) & "(" & Chr(CurrentCharNum + 37)
         End Select
    Next I
    'Process add-ons if they exist
    If Len(EAN2AddOn) = 2 Then DataToPrint = DataToPrint & ProcessEAN2AddOn(EAN2AddOn)
    If Len(EAN5AddOn) = 5 Then DataToPrint = DataToPrint & ProcessEAN5AddOn(EAN5AddOn)
    'Return PrintableString
    UPCa = DataToPrint
End Function

Private Function UPCe7To11(DataToExpand As String) As String
    '2005.12 BDA added this function
    'This function expands 7 digits to the UPC-A 11 needed
    'If 6 digits entered, assume the number system is 0
    '2006.2 BDA added the next line for compatibility with different office versions
    StringLength = Len(DataToExpand)    
    If StringLength = 6 Then DataToExpand = "0" & DataToExpand
    'Expect 7 digits; the first digit is the number system
    If StringLength <> 7 Then DataToExpand = "0000000"
    Dim D1 As String
    Dim D2 As String
    Dim D3 As String
    Dim D4 As String
    Dim D5 As String
    Dim D6 As String
    Dim D7 As String
    D1 = Mid(DataToExpand, 1, 1)
    D2 = Mid(DataToExpand, 2, 1)
    D3 = Mid(DataToExpand, 3, 1)
    D4 = Mid(DataToExpand, 4, 1)
    D5 = Mid(DataToExpand, 5, 1)
    D6 = Mid(DataToExpand, 6, 1)
    D7 = Mid(DataToExpand, 7, 1)
    Select Case D7
    Case "0"
        UPCe7To11 = D1 & D2 & D3 & "00000" & D4 & D5 & D6
    Case "1"
        UPCe7To11 = D1 & D2 & D3 & D7 & "0000" & D4 & D5 & D6
    Case "2"
        UPCe7To11 = D1 & D2 & D3 & D7 & "0000" & D4 & D5 & D6
    Case "3"
        UPCe7To11 = D1 & D2 & D3 & D4 & "00000" & D5 & D6
    Case "4"
        UPCe7To11 = D1 & D2 & D3 & D4 & D5 & "00000" & D6
    Case "5"
        UPCe7To11 = D1 & D2 & D3 & D4 & D5 & D6 & "0000" & D7
    Case "6"
        UPCe7To11 = D1 & D2 & D3 & D4 & D5 & D6 & "0000" & D7
    Case "7"
        UPCe7To11 = D1 & D2 & D3 & D4 & D5 & D6 & "0000" & D7
    Case "8"
        UPCe7To11 = D1 & D2 & D3 & D4 & D5 & D6 & "0000" & D7
    Case "9"
        UPCe7To11 = D1 & D2 & D3 & D4 & D5 & D6 & "0000" & D7
    End Select
    '2006.2 BDA modified the next line for compatibility with different office versions
    StringLength = Len(UPCe7To11)
End Function

Public Function UPCe(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    OnlyCorrectData = ""
    DataToPrint = ""
    ' Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
         'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    'If UPCe is not expanded, then expand
    '2006.2 BDA added the next 4 lines for compatibility with different office versions
    StringLength = Len(OnlyCorrectData)        
    If StringLength = 6 Then OnlyCorrectData = UPCe7To11("0" & OnlyCorrectData)
    If StringLength = 7 Then OnlyCorrectData = UPCe7To11(OnlyCorrectData)
    If StringLength = 8 Then OnlyCorrectData = UPCe7To11(Mid(OnlyCorrectData, 1, 7))

    '2006.2 BDA added the next line for compatibility with different office versions
    StringLength = Len(OnlyCorrectData)            
    If StringLength < 11 Then OnlyCorrectData = "00005000000"
    If StringLength = 15 Then OnlyCorrectData = "00005000000"
    If StringLength > 18 Then OnlyCorrectData = "00005000000"
    If StringLength = 12 Then OnlyCorrectData = Mid(OnlyCorrectData, 1, 11)
    If StringLength = 14 Then OnlyCorrectData = (Mid(OnlyCorrectData, 1, 11) & Mid(OnlyCorrectData, 13, 2))
    If StringLength = 17 Then OnlyCorrectData = (Mid(OnlyCorrectData, 1, 11) & Mid(OnlyCorrectData, 13, 5))
    EAN2AddOn = ""
    EAN5AddOn = ""
    EANAddOnToPrint = ""
    '2006.2 BDA added the next line for compatibility with different office versions
    StringLength = Len(OnlyCorrectData)           
    If StringLength  = 16 Then EAN5AddOn = Mid(OnlyCorrectData, 12, 5)
    If StringLength = 13 Then EAN2AddOn = Mid(OnlyCorrectData, 12, 2)
    'split 12 digit number from add-on
    DataToEncode = Mid(OnlyCorrectData, 1, 11)
    'Calculate Check Digit
    Factor = 3
    WeightedTotal = 0
    For I = Len(DataToEncode) To 1 Step -1
       'Get the value of each number starting at the end
       CurrentCharNum = Mid(DataToEncode, I, 1)
       'Multiply by the weighting factor which is 3,1,3,1...
       'and add the sum together
       WeightedTotal = WeightedTotal + CurrentCharNum * Factor
       'Change the factor for next calculation
       Factor = 4 - Factor
    Next I
    'Find the CheckDigit by finding the number + WeightedTotal that = a multiple of 10
    'Divide by 10, get the remainder and subtract from 10
    I = (WeightedTotal Mod 10)
    If I <> 0 Then
         CheckDigit = (10 - I)
    Else
         CheckDigit = 0
    End If
    DataToEncode = DataToEncode & CheckDigit
    'Compress UPC-A to UPC-E if possible
    Dim D1 As String
    Dim D2 As String
    Dim D3 As String
    Dim D4 As String
    Dim D5 As String
    Dim D6 As String
    Dim D7 As String
    Dim D8 As String
    Dim D9 As String
    Dim D10 As String
    Dim D11 As String
    Dim D12 As String
    D1 = Mid(DataToEncode, 1, 1)
    D2 = Mid(DataToEncode, 2, 1)
    D3 = Mid(DataToEncode, 3, 1)
    D4 = Mid(DataToEncode, 4, 1)
    D5 = Mid(DataToEncode, 5, 1)
    D6 = Mid(DataToEncode, 6, 1)
    D7 = Mid(DataToEncode, 7, 1)
    D8 = Mid(DataToEncode, 8, 1)
    D9 = Mid(DataToEncode, 9, 1)
    D10 = Mid(DataToEncode, 10, 1)
    D11 = Mid(DataToEncode, 11, 1)
    D12 = Mid(DataToEncode, 12, 1)
    '2005.12 BDA updated the next line
    If D1 = "0" Or D1 = "1" Then
       'Condition A
       'EX: 02345600007
        If (D11 = "5" Or D11 = "6" Or D11 = "7" Or D11 = "8" Or D11 = "9") And D6 <> "0" And (D7 = "0" And D8 = "0" And D9 = "0" And D10 = "0") Then
             DataToEncode = D2 & D3 & D4 & D5 & D6 & D11
        End If
       'Condition B
       'EX: 02345000001
        If (D6 = "0" And D7 = "0" And D8 = "0" And D9 = "0" And D10 = "0") And D5 <> "0" Then
             DataToEncode = D2 & D3 & D4 & D5 & D11 & "4"
        End If
       'Condition C
       'EX: 06320000971
        If (D5 = "0" And D6 = "0" And D7 = "0" And D8 = "0") And (D4 = "1" Or D4 = "2" Or D4 = "0") Then
             DataToEncode = D2 & D3 & D9 & D10 & D11 & D4
        End If
       'Condition D
       'EX: 08670000093
        If (D5 = "0" And D6 = "0" And D7 = "0" And D8 = "0" And D9 = "0") And (D4 = "3" Or D4 = "4" Or D4 = "5" Or D4 = "6" Or D4 = "7" Or D4 = "8" Or D4 = "9") Then
             DataToEncode = D2 & D3 & D4 & D10 & D11 & "3"
        End If
    End If
    'Run UPC-E compression only if DataToEncode = 6
    If Len(DataToEncode) = 6 Then
        '2005.12 BDA updated this section for number system 1 compatibility
        'Encode the check character into the symbol
        'by using variable parity between character sets A and B
        'The UPC-E starts with a 0 or 1 which indicates the number system
        'Number system is 1 only if first digit is 1
        If D1 = "1" Then
            Select Case D12
            Case "0"
                 Encoding = "AAABBB"
            Case "1"
                 Encoding = "AABABB"
            Case "2"
                 Encoding = "AABBAB"
            Case "3"
                 Encoding = "AABBBA"
            Case "4"
                 Encoding = "ABAABB"
            Case "5"
                 Encoding = "ABBAAB"
            Case "6"
                 Encoding = "ABBBAA"
            Case "7"
                 Encoding = "ABABAB"
            Case "8"
                 Encoding = "ABABBA"
            Case "9"
                 Encoding = "ABBABA"
            End Select
        Else
            'Number system 0
            D1 = "0"
            Select Case D12
            Case "0"
                 Encoding = "BBBAAA"
            Case "1"
                 Encoding = "BBABAA"
            Case "2"
                 Encoding = "BBAABA"
            Case "3"
                 Encoding = "BBAAAB"
            Case "4"
                 Encoding = "BABBAA"
            Case "5"
                 Encoding = "BAABBA"
            Case "6"
                 Encoding = "BAAABB"
            Case "7"
                 Encoding = "BABABA"
            Case "8"
                 Encoding = "BABAAB"
            Case "9"
                 Encoding = "BAABAB"
            End Select
        End If
        StringLength = Len(DataToEncode)
        For I = 1 To StringLength
              'Get the ASCII value of each number
              CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
              CurrentEncoding = Mid(Encoding, I, 1)
              Select Case CurrentEncoding
              Case "A"
                  DataToPrint = DataToPrint & Chr(CurrentCharNum)
              Case "B"
                  DataToPrint = DataToPrint & Chr(CurrentCharNum + 17)
              End Select
              'Add in the 1st character along with guard patterns at the correct locations
              Select Case I
              Case 1
                  'For the LeadingDigit print the human readable character of the number system,
                  'the normal guard pattern and then the rest of the barcode
                  If D1 = "0" Then DataToPrint = Chr(85) & "(" & DataToPrint
                  If D1 = "1" Then DataToPrint = Chr(86) & "(" & DataToPrint
              Case 6
                  'Print the SPECIAL guard pattern and check character
                  If CInt(D12) > 4 Then DataToPrint = DataToPrint & ")" & Chr(Asc(D12) + 64)
                  If CInt(D12) < 5 Then DataToPrint = DataToPrint & ")" & Chr(Asc(D12) + 37)
             End Select
        Next I
    End If
 
    If Len(DataToEncode) <> 6 Then
        StringLength = Len(DataToEncode)
        For I = 1 To StringLength
            'Get the ASCII value of each number
            CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
            Select Case I
            Case 1
                'For the first character, print the human readable character, the normal
                'guard pattern, and then the barcode without the human readable character
                If Chr(CurrentCharNum) > 4 Then DataToPrint = Chr(CurrentCharNum + 64) & "(" & Chr(CurrentCharNum + 49)
                If Chr(CurrentCharNum) < 5 Then DataToPrint = Chr(CurrentCharNum + 37) & "(" & Chr(CurrentCharNum + 49)
            Case 2
                DataToPrint = DataToPrint & Chr(CurrentCharNum)
            Case 3
                DataToPrint = DataToPrint & Chr(CurrentCharNum)
            Case 4
                DataToPrint = DataToPrint & Chr(CurrentCharNum)
            Case 5
                DataToPrint = DataToPrint & Chr(CurrentCharNum)
            Case 6
                'Print the center guard pattern after the 6th character
                DataToPrint = DataToPrint & Chr(CurrentCharNum) & "*"
            Case 7
                'Add 27 to the ASII value of characters 6-12 to print from character set C
                'This is required when printing to the right of the center guard pattern
                DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
            Case 8
                DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
            Case 9
                DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
            Case 10
                DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
            Case 11
                DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
            Case 12
                'For the last character, print the barcode without the human readable character,
                'the normal guard pattern, and then the human readable character.
            If Chr(CurrentCharNum) > 4 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 59) & "(" & Chr(CurrentCharNum + 64)
            If Chr(CurrentCharNum) < 5 Then DataToPrint = DataToPrint & Chr(CurrentCharNum + 59) & "(" & Chr(CurrentCharNum + 37)
            End Select
        Next I
    End If
     
    'Process add-ons if they exist
    If Len(EAN2AddOn) = 2 Then DataToPrint = DataToPrint & ProcessEAN2AddOn(EAN2AddOn)
    If Len(EAN5AddOn) = 5 Then DataToPrint = DataToPrint & ProcessEAN5AddOn(EAN5AddOn)
    'Return PrintableString
    UPCe = DataToPrint
End Function

Public Function EAN13(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************   
    DataToPrint = ""
    OnlyCorrectData = ""
    'Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
         'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    '2006.2 BDA added the next line for general compatibility
    StringLength = Len(OnlyCorrectData)       
    If StringLength < 12 Then OnlyCorrectData = "0000000000000"
    If StringLength = 16 Then OnlyCorrectData = "0000000000000"
    If StringLength = 13 Then OnlyCorrectData = Mid(OnlyCorrectData, 1, 12)
    If StringLength = 15 Then OnlyCorrectData = (Mid(OnlyCorrectData, 1, 12) & Mid(OnlyCorrectData, 14, 2))
    If StringLength > 17 Then OnlyCorrectData = (Mid(OnlyCorrectData, 1, 12) & Mid(OnlyCorrectData, 14, 5))
    Dim EAN2AddOn As String
    Dim EAN5AddOn As String
    EAN2AddOn = ""
    EAN5AddOn = ""
    '2006.2 BDA added the next line for general compatibility
    StringLength = Len(OnlyCorrectData)      
    If StringLength = 17 Then EAN5AddOn = Mid(OnlyCorrectData, 13, 5)
    If StringLength = 14 Then EAN2AddOn = Mid(OnlyCorrectData, 13, 2)
    'Remove digit number from add-ons and check digit
    DataToEncode = Mid(OnlyCorrectData, 1, 12)
    'Calculate Check Digit
    Factor = 3
    WeightedTotal = 0
    For I = Len(DataToEncode) To 1 Step -1
        'Get the value of each number starting at the end
        CurrentCharNum = Mid(DataToEncode, I, 1)
        'Multiply by the weighting factor which is 3,1,3,1...
        'and add the sum together
        WeightedTotal = WeightedTotal + CurrentCharNum * Factor
        'Change factor for next calculation
        Factor = 4 - Factor
    Next I
    'Find the CheckDigit by finding the number + WeightedTotal that = a multiple of 10
    'Divide by 10, get the remainder and subtract from 10
    I = (WeightedTotal Mod 10)
    If I <> 0 Then
        CheckDigit = (10 - I)
    Else
        CheckDigit = 0
    End If
    'Encode the leading digit into the left half of the EAN-13 symbol
    'by using variable parity between character sets A and B
    LeadingDigit = Mid(DataToEncode, 1, 1)
    Select Case LeadingDigit
    Case 0
    Encoding = "AAAAAACCCCCC"
    Case 1
    Encoding = "AABABBCCCCCC"
    Case 2
    Encoding = "AABBABCCCCCC"
    Case 3
    Encoding = "AABBBACCCCCC"
    Case 4
    Encoding = "ABAABBCCCCCC"
    Case 5
    Encoding = "ABBAABCCCCCC"
    Case 6
    Encoding = "ABBBAACCCCCC"
    Case 7
    Encoding = "ABABABCCCCCC"
    Case 8
    Encoding = "ABABBACCCCCC"
    Case 9
    Encoding = "ABBABACCCCCC"
    End Select
    'Add the check digit to the end of the barcode & remove the leading digit
    DataToEncode = Mid(DataToEncode, 2, 11) & CheckDigit
    'Determine character to print for proper barcoding
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
    'Get the ASCII value of each number excluding the first number because
    'it is encoded with variable parity
    CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
    CurrentEncoding = Mid(Encoding, I, 1)
    'Print different barcodes according to the location of the CurrentChar and CurrentEncoding
    Select Case CurrentEncoding
    Case "A"
         DataToPrint = DataToPrint & Chr(CurrentCharNum)
    Case "B"
         DataToPrint = DataToPrint & Chr(CurrentCharNum + 17)
    Case "C"
         DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
    End Select
    'Add in the 1st character along with guard patterns
    Select Case I
    Case 1
        'For the LeadingDigit, print the human readable character,
        'the normal guard pattern, and then the rest of the barcode
        If LeadingDigit > 4 Then DataToPrint = Chr((LeadingDigit + 48) + 64) & "(" & DataToPrint
        If LeadingDigit < 5 Then DataToPrint = Chr((LeadingDigit + 48) + 37) & "(" & DataToPrint
    Case 6
        'Print the center guard pattern after the 6th character
        DataToPrint = DataToPrint & "*"
    Case 12
        'For the last character (12), print the the normal guard pattern after the barcode
        DataToPrint = DataToPrint & "("
    End Select
    Next I
    'Process add-ons if they exist
    If Len(EAN2AddOn) = 2 Then DataToPrint = DataToPrint & " " & ProcessEAN2AddOn(EAN2AddOn)
    If Len(EAN5AddOn) = 5 Then DataToPrint = DataToPrint & " " & ProcessEAN5AddOn(EAN5AddOn)
    'Return PrintableString
    EAN13 = DataToPrint
End Function


Public Function EAN8(DataToEncode As String) As String
'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
    DataToPrint = ""
    OnlyCorrectData = ""
    'Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
         'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    '2006.2 BDA modified the next 14 lines for add-on compatibility
    StringLength = Len(OnlyCorrectData)       
    If StringLength < 7 Then OnlyCorrectData = "0000000"
    If StringLength = 11 Then OnlyCorrectData = "0000000"
    If StringLength = 8 Then OnlyCorrectData = Mid(OnlyCorrectData, 1, 7)
    If StringLength = 10 Then OnlyCorrectData = (Mid(OnlyCorrectData, 1, 7) & Mid(OnlyCorrectData, 9, 2))
    If StringLength > 12 Then OnlyCorrectData = (Mid(OnlyCorrectData, 1, 7) & Mid(OnlyCorrectData, 9, 5))
    Dim EAN2AddOn As String
    Dim EAN5AddOn As String
    EAN2AddOn = ""
    EAN5AddOn = ""
    '2006.2 BDA added the next line for general compatibility
    StringLength = Len(OnlyCorrectData)      
    If StringLength = 12 Then EAN5AddOn = Mid(OnlyCorrectData, 8, 5)
    If StringLength = 9 Then EAN2AddOn = Mid(OnlyCorrectData, 8, 2)
    'Remove digit number from add-ons and check digit
    DataToEncode = Mid(OnlyCorrectData, 1, 7)
    'Calculate Check Digit
    Factor = 3
    WeightedTotal = 0
    For I = Len(DataToEncode) To 1 Step -1
        'Get the value of each number starting at the end
        CurrentCharNum = Mid(DataToEncode, I, 1)
        'Multiply by the weighting factor which is 3,1,3,1...
        'and add the sum together
        WeightedTotal = WeightedTotal + CurrentCharNum * Factor
        'Change factor for next calculation
        Factor = 4 - Factor
    Next I
    'Find the CheckDigit by finding the number + WeightedTotal that = a multiple of 10
    'Divide by 10, get the remainder and subtract from 10
    I = (WeightedTotal Mod 10)
    If I <> 0 Then
        CheckDigit = (10 - I)
    Else
        CheckDigit = 0
    End If
    DataToEncode = DataToEncode & CheckDigit
    'Determine character to print for proper barcoding
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
        'Get the ASCII value of each number
        CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
        CurrentEncoding = Mid(Encoding, I, 1)
        'Print different barcodes according to the location of the CurrentChar and CurrentEncoding
        Select Case I
        Case 1
        'For the first character, print the normal guard pattern
        'and then the barcode, without the human readable character
             DataToPrint = "(" & Chr(CurrentCharNum)
        Case 2
             DataToPrint = DataToPrint & Chr(CurrentCharNum)
        Case 3
             DataToPrint = DataToPrint & Chr(CurrentCharNum)
        Case 4
        'Print the center guard pattern after the 6th character
             DataToPrint = DataToPrint & Chr(CurrentCharNum) & "*"
        Case 5
             DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
        Case 6
             DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
        Case 7
             DataToPrint = DataToPrint & Chr(CurrentCharNum + 27)
        Case 8
        'Print the check digit as 8th character + normal guard pattern
             DataToPrint = DataToPrint & Chr(CurrentCharNum + 27) & "("
        End Select
    Next I
    '2006.2 BDA modified the next 3 lines for add-on compatibility    
    'Process add-ons if they exist
    If Len(EAN2AddOn) = 2 Then DataToPrint = DataToPrint & " " & ProcessEAN2AddOn(EAN2AddOn)
    If Len(EAN5AddOn) = 5 Then DataToPrint = DataToPrint & " " & ProcessEAN5AddOn(EAN5AddOn)
    'Return PrintableString
    EAN8 = DataToPrint
End Function


Public Function UCC128(UCCToEncode As String) As String
    'Check for FNC1 which can be ASCII 202 and ASCII 212-217
    CurrentCharNum = Asc(Mid(UCCToEncode, 1, 1))
    If ((CurrentCharNum = 202) Or ((CurrentCharNum > 211) And (CurrentCharNum < 219))) Then
        'do nothing, AI is already in the data
    Else
        UCCToEncode = Chr(202) & UCCToEncode
    End If
    UCC128 = Code128(UCCToEncode, 0, True)
End Function


Public Function Code11(DataToEncode As String) As String
    DataToPrint = ""
    OnlyCorrectData = ""
    ' Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
         'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 2 lines for compatibility with different office versions
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If (CurrentCharNum > 47 And CurrentCharNum < 58) or CurrentCharNum = 45  Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    DataToEncode = OnlyCorrectData
    'Calculate Check Digit 
    Factor = 1
    WeightedTotal = 0
    For I = Len(DataToEncode) To 1 Step -1
        'Get the value of each number starting at the end
         CurrentChar = Mid(DataToEncode, I, 1)
        'Set the "-" character to the value of 10
         If CurrentChar = "-" Then CurrentChar = "10"
        'Multiply by the weighting character and add together
         WeightedTotal = WeightedTotal + Val(CurrentChar) * Factor
        'Change factor for next calculation
         Factor = Factor + 1
    Next I
    'Find the Modulo 11 check digit
    CheckDigit = WeightedTotal Mod 11
    Code11 = "(" & DataToEncode & CheckDigit & ")"
End Function


Public Function RM4SCC(DataToEncode As String) As String
    '*********************************************************************
    '*  VB Functions for IDAutomation Barcode Fonts v2006.2
    '*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
    '*
    '*  Visit http://www.idautomation.com/openoffice/ for more
    '*  information about the functions in this file.
    '*
    '*  You may incorporate our Source Code in your application
    '*  only if you own a valid license from IDAutomation.com, Inc.
    '*  for the associated font and this text and the copyright notices
    '*  are not removed from the source code.
    '*
    '*  Distributing our source code or fonts outside your
    '*  organization requires a Developer License.
    '*********************************************************************
    DataToEncode = UCase(DataToEncode)
    OnlyCorrectData = ""
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
         'Get each character one at a time
         CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         'Get the value of CurrentChar according to MOD43
         '0-9
         If CurrentCharNum < 58 And CurrentCharNum > 47 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
         'A-Z
         If CurrentCharNum < 91 And CurrentCharNum > 64 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I
    DataToEncode = OnlyCorrectData   
    Dim r As Integer
    Dim C As Integer
    Dim Rtotal As Long
    Dim Ctotal As Long
    Rtotal = 0
    Ctotal = 0
    WeightedTotal = 0
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
    'Get each character one at a time
         CurrentChar = Mid(DataToEncode, I, 1)
    'Get the values of CurrentChar
         Select Case CurrentChar
         Case "0"
              r = 1
              C = 1
         Case "1"
              r = 1
              C = 2
         Case "2"
              r = 1
              C = 3
         Case "3"
              r = 1
              C = 4
         Case "4"
              r = 1
              C = 5
         Case "5"
              r = 1
              C = 0
         Case "6"
              r = 2
              C = 1
         Case "7"
              r = 2
              C = 2
         Case "8"
              r = 2
              C = 3
         Case "9"
              r = 2
              C = 4
         Case "A"
              r = 2
              C = 5
         Case "B"
              r = 2
              C = 0
         Case "C"
              r = 3
              C = 1
         Case "D"
              r = 3
              C = 2
         Case "E"
              r = 3
              C = 3
         Case "F"
              r = 3
              C = 4
         Case "G"
              r = 3
              C = 5
         Case "H"
              r = 3
              C = 0
         Case "I"
              r = 4
              C = 1
         Case "J"
              r = 4
              C = 2
         Case "K"
              r = 4
              C = 3
         Case "L"
              r = 4
              C = 4
         Case "M"
              r = 4
              C = 5
         Case "N"
              r = 4
              C = 0
         Case "O"
              r = 5
              C = 1
         Case "P"
              r = 5
              C = 2
         Case "Q"
              r = 5
              C = 3
         Case "R"
              r = 5
              C = 4
         Case "S"
              r = 5
              C = 5
         Case "T"
              r = 5
              C = 0
         Case "U"
              r = 0
              C = 1
         Case "V"
              r = 0
              C = 2
         Case "W"
              r = 0
              C = 3
         Case "X"
              r = 0
              C = 4
         Case "Y"
              r = 0
              C = 5
         Case "Z"
              r = 0
              C = 0
              
         End Select
    'add the values together
         Rtotal = Rtotal + r
         Ctotal = Ctotal + C
    Next I
    
    'divide the Totals by 6 and get the remainder, this is a reference
    'to the Check Digit.
    'set check digit to CurrentChar (a string)
    Rtotal = (Rtotal Mod 6)
    Ctotal = (Ctotal Mod 6)
    Select Case Rtotal
    Case 1
         Select Case Ctotal
         Case 1
              CurrentChar = "0"
         Case 2
              CurrentChar = "1"
         Case 3
              CurrentChar = "2"
         Case 4
              CurrentChar = "3"
         Case 5
              CurrentChar = "4"
         Case 0
              CurrentChar = "5"
         End Select
    Case 2
         Select Case Ctotal
         Case 1
              CurrentChar = "6"
         Case 2
              CurrentChar = "7"
         Case 3
              CurrentChar = "8"
         Case 4
              CurrentChar = "9"
         Case 5
              CurrentChar = "A"
         Case 0
              CurrentChar = "B"
         End Select
    Case 3
         Select Case Ctotal
         Case 1
              CurrentChar = "C"
         Case 2
              CurrentChar = "D"
         Case 3
              CurrentChar = "E"
         Case 4
              CurrentChar = "F"
         Case 5
              CurrentChar = "G"
         Case 0
              CurrentChar = "H"
         End Select
    Case 4
         Select Case Ctotal
         Case 1
              CurrentChar = "I"
         Case 2
              CurrentChar = "J"
         Case 3
              CurrentChar = "K"
         Case 4
              CurrentChar = "L"
         Case 5
              CurrentChar = "M"
         Case 0
              CurrentChar = "N"
         End Select
    Case 5
         Select Case Ctotal
         Case 1
              CurrentChar = "O"
         Case 2
              CurrentChar = "P"
         Case 3
              CurrentChar = "Q"
         Case 4
              CurrentChar = "R"
         Case 5
              CurrentChar = "S"
         Case 0
              CurrentChar = "T"
         End Select
    Case 0
         Select Case Ctotal
         Case 1
              CurrentChar = "U"
         Case 2
              CurrentChar = "V"
         Case 3
              CurrentChar = "W"
         Case 4
              CurrentChar = "X"
         Case 5
              CurrentChar = "Y"
         Case 0
              CurrentChar = "Z"
         End Select
    End Select
    'Return Printable String
    RM4SCC = "(" & DataToEncode & CurrentChar & ")"
End Function


Public Function Codabar(DataToEncode As String) As String
    '*********************************************************************
    '*  VB Functions for IDAutomation Barcode Fonts v2006.2
    '*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
    '*
    '*  Visit http://www.idautomation.com/openoffice/ for more
    '*  information about the functions in this file.
    '*
    '*  You may incorporate our Source Code in your application
    '*  only if you own a valid license from IDAutomation.com, Inc.
    '*  for the associated font and this text and the copyright notices
    '*  are not removed from the source code.
    '*
    '*  Distributing our source code or fonts outside your
    '*  organization requires a Developer License.
    '*********************************************************************
    DataToPrint = ""
    OnlyCorrectData = ""
    StringLength = Len(DataToEncode)
    'Check to make sure data is numeric, $, +, -, /, or :, and remove all others.    
    For I = 1 To StringLength
         '2006.2 BDA modified the next 9 lines for compatibility with different office versions    
	     CurrentChar = Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(CurrentChar)	     
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
         If CurrentChar = "$" Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
         If CurrentChar = "+" Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
         If CurrentChar = "-" Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
         If CurrentChar = "/" Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
         If CurrentChar = "." Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
         If CurrentChar = ":" Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I
    DataToPrint = OnlyCorrectData
    'Get Printable String
    Codabar = "A" & DataToPrint & "B"
End Function



Public Function Postnet(DataToEncode As String, ReturnType As Integer) As String
    '*********************************************************************
    '*  VB Functions for IDAutomation Barcode Fonts v2006.2
    '*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
    '*
    '*  Visit http://www.idautomation.com/openoffice/ for more
    '*  information about the functions in this file.
    '*
    '*  You may incorporate our Source Code in your application
    '*  only if you own a valid license from IDAutomation.com, Inc.
    '*  for the associated font and this text and the copyright notices
    '*  are not removed from the source code.
    '*
    '*  Distributing our source code or fonts outside your
    '*  organization requires a Developer License.
    '*********************************************************************
    'Additional logic in case ReturnType is not correct
    If ReturnType <> 0 And ReturnType <> 1 And ReturnType <> 2 Then ReturnType = 0
    DataToPrint = ""
    OnlyCorrectData = ""    
    DataToEncode = RTrim(LTrim(DataToEncode))
    'Check to make sure data is numeric and remove dashes, etc.
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
         'Add all numbers to OnlyCorrectData string
         '2006.2 BDA modified the next 3 lines for compatibility with different office versions
         'If IsNumeric(Mid(DataToEncode, I, 1)) Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
	     CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then OnlyCorrectData = OnlyCorrectData & Mid(DataToEncode, I, 1)
    Next I 
    DataToEncode = OnlyCorrectData
    'Calculate Check Digit
    WeightedTotal = 0
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
         'Get the value of each number
         CurrentCharNum = Mid(DataToEncode, I, 1)
         'Add the values together
         WeightedTotal = WeightedTotal + CurrentCharNum
    Next I
    'Find the CheckDigit by finding the number + WeightedTotal that = a multiple of 10
    'divide by 10, get the remainder and subtract from 10
    I = (WeightedTotal Mod 10)
    If I <> 0 Then
         CheckDigit = (10 - I)
    Else
         CheckDigit = 0
    End If
    'ReturnType 0 returns data formatted to the barcode font
    If ReturnType = 0 Then Postnet = "(" & DataToEncode & CheckDigit & ")"
    'ReturnType 1 returns data formatted for human readable text
    If ReturnType = 1 Then Postnet = DataToEncode & CheckDigit
    'ReturnType 2 returns the  check digit for the data supplied
    If ReturnType = 2 Then Postnet = Str$(CheckDigit)
End Function


Public Function Code93(DataToEncode As String) As String
    '*********************************************************************
    '*  VB Functions for IDAutomation Barcode Fonts v2006.2
    '*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
    '*
    '*  Visit http://www.idautomation.com/openoffice/ for more
    '*  information about the functions in this file.
    '*
    '*  You may incorporate our Source Code in your application
    '*  only if you own a valid license from IDAutomation.com, Inc.
    '*  for the associated font and this text and the copyright notices
    '*  are not removed from the source code.
    '*
    '*  Distributing our source code or fonts outside your
    '*  organization requires a Developer License.
    '*********************************************************************
    DataToEncode = UCase(DataToEncode)
    DataToPrint = ""
    OnlyCorrectData = ""
    'Only pass correct data
    StringLength = Len(DataToEncode)
    For I = 1 To StringLength
        CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
        If Code93Val(CurrentCharNum) < 47 Then
            If CurrentCharNum = 32 Then CurrentCharNum = 61
            OnlyCorrectData = OnlyCorrectData & Chr(CurrentCharNum)
        End If
    Next I
    DataToEncode = OnlyCorrectData    
    CurrentCharNum = 0
    StringLength = Len(DataToEncode)
    Dim C As Integer
    Dim K As Integer
    Dim CW As Integer
    Dim KW As Integer
    Dim CWSum As Integer
    Dim KWSum As Integer
    CW = 1
    KW = 2
    I = 1
    '2006.2 BDA modified the next line for compatibility with different office versions    
    For I = StringLength To 1 Step -1
       'Get each character one at a time from the back
       CurrentCharNum = Asc(Mid(DataToEncode, I, 1))
       'Get the value
       CurrentValue = Code93Val(CurrentCharNum)
       'Calculate check digit C
       CWSum = CWSum + (CurrentValue * CW)
       CW = CW + 1
       If CW = 21 Then CW = 1
       'Calculate check digit K
       KWSum = KWSum + (CurrentValue * KW)
       KW = KW + 1
       If KW = 16 Then KW = 1
       'Gather data to print
       DataToPrint = Chr(CurrentCharNum) & DataToPrint
    Next I
    'Divide the C sum by 47 and get the remainder, this is the Check Digit
    C = (CWSum Mod 47)
    'Add the last digit to the K sum
    KWSum = KWSum + C
    'Divide the K sum by 47 and get the remainder, this is the Check Digit
    K = (KWSum Mod 47)
    Code93 = "(" & DataToPrint & Code93Char(C) & Code93Char(K) & ")"
End Function

Private Function Code93Char(CharValue As Integer) As String
    'Returns a character from a character value
    'An invalid character value returns nothing
    Code93Char = ""
    If CharValue < 10 And CharValue > -1 Then Code93Char = Chr(CharValue + 48)
    'A-Z
    If CharValue < 36 And CharValue > 9 Then Code93Char = Chr(CharValue + 55)
    '-
    If CharValue = 36 Then Code93Char = Chr(45)
    '.
    If CharValue = 37 Then Code93Char = "."
    'Space
    If CharValue = 38 Then Code93Char = "="
    '$
    If CharValue = 39 Then Code93Char = "$"
    '/
    If CharValue = 40 Then Code93Char = "/"
    '+
    If CharValue = 41 Then Code93Char = "+"
    '%
    If CharValue = 42 Then Code93Char = "%"
    '!
    If CharValue = 43 Then Code93Char = "!"
    '#
    If CharValue = 44 Then Code93Char = "#"
    '&
    If CharValue = 45 Then Code93Char = "&"
    '@
    If CharValue = 46 Then Code93Char = "@"
End Function

Private Function Code93Val(CharASCValue As Integer) As Integer
    'Returns a character value from a character
    'An invalid character value returns 99
    Code93Val = 99
    '0-9
    If CharASCValue < 58 And CharASCValue > 47 Then Code93Val = CharASCValue - 48
    'A-Z
    If CharASCValue < 91 And CharASCValue > 64 Then Code93Val = CharASCValue - 55
    'Space
    If CharASCValue = 32 Then Code93Val = 38
    '=
    If CharASCValue = 61 Then Code93Val = 38
    '-
    If CharASCValue = 45 Then Code93Val = 36
    '.
    If CharASCValue = 46 Then Code93Val = 37
    '$
    If CharASCValue = 36 Then Code93Val = 39
    '/
    If CharASCValue = 47 Then Code93Val = 40
    '+
    If CharASCValue = 43 Then Code93Val = 41
    '%
    If CharASCValue = 37 Then Code93Val = 42
    '!
    If CharASCValue = 33 Then Code93Val = 43
    '#
    If CharASCValue = 35 Then Code93Val = 44
    '&
    If CharASCValue = 38 Then Code93Val = 45
    '@
    If CharASCValue = 64 Then Code93Val = 46
End Function


Public Function SpliceText(DataToFormat As String, SpacingNumber As Integer, ApplyTilde as Boolean) As String
    'This function inserts a space for every SpacingNumber of characters
    '2006.2 BDA added the next line to move code to the ProcessTilde function
    If ApplyTilde Then DataToFormat = ProcessTilde(DataToFormat)
    HumanReadableText = ""
    StringLength = Len(DataToFormat)
    J = 0
    For I = 1 To StringLength
        CurrentCharNum = Asc(Mid(DataToFormat, I, 1))
        If CurrentCharNum > 31 And CurrentCharNum < 128 Then
            HumanReadableText = HumanReadableText & Mid(DataToFormat, I, 1)
            J = J + 1
        End If
        If (J Mod SpacingNumber) = 0 Then HumanReadableText = HumanReadableText & " "
    Next I
    SpliceText = HumanReadableText
End Function


Public Function MOD10(M10NumberData As String) As Integer
'***********************************************************************
' This is a general MOD10 function compatible with EAN and UPC standards
'***********************************************************************
     Dim M10StringLength As Integer
     Dim M10OnlyCorrectData As String
     Dim M10Factor As Integer
     Dim M10WeightedTotal As Integer
     Dim M10CheckDigit As Integer
     Dim M10I As Integer
     M10OnlyCorrectData = ""
     M10StringLength = Len(M10NumberData)    
     'Check to make sure data is numeric and remove dashes, etc.
     For M10I = 1 To M10StringLength
        'Add all numbers to OnlyCorrectData string
        '2006.2 BDA modified the next 2 lines for compatibility with different office versions
	     CurrentCharNum = Asc(Mid(M10NumberData, M10I, 1)
         If CurrentCharNum > 47 And CurrentCharNum < 58 Then M10OnlyCorrectData = M10OnlyCorrectData & Mid(M10NumberData, M10I, 1)
     Next M10I
    'Generate MOD 10 check digit
     M10Factor = 3
     M10WeightedTotal = 0
     M10StringLength = Len(M10NumberData)
     For M10I = M10StringLength To 1 Step -1
    'Get the value of each number starting at the end
    'CurrentCharNum = Mid(M10NumberData, I, 1)
    'Multiply by the weighting factor which is 3,1,3,1...
    'and add the sum together
          M10WeightedTotal = M10WeightedTotal + (Val(Mid(M10NumberData, M10I, 1)) * M10Factor)
          'Change factor for next calculation
          M10Factor = 4 - M10Factor
     Next M10I
    'Find the CheckDigit by finding the smallest number that = a multiple of 10
     M10I = (M10WeightedTotal Mod 10)
     If M10I <> 0 Then
          M10CheckDigit = (10 - M10I)
     Else
          M10CheckDigit = 0
     End If
     MOD10 = Str(M10CheckDigit)
End Function


Public Function ProcessTilde(StringToProcess as string) as string
        ProcessTilde = ""
        Dim OutString as string
        StringLength = Len(StringToProcess)
        For I = 1 To StringLength
            If (I < StringLength - 2) And Mid(StringToProcess, I, 2) = "~m" And IsNumeric(Mid(StringToProcess, I + 2, 2)) Then
                WeightValue = Val(Mid(StringToProcess, I + 2, 2))
                If (I - WeightValue) < 1 Then WeightValue = I - 1
                CheckDigitValue = MOD10(Mid(StringToProcess, I - WeightValue, WeightValue))
                OutString = OutString & Chr(CheckDigitValue + 48)
                I = I + 3
            ElseIf (I < StringLength - 2) And Mid(StringToProcess, I, 1) = "~" And IsNumeric(Mid(StringToProcess, I + 1, 3)) Then
                CurrentCharNum = Val(Mid(StringToProcess, I + 1, 3))
                OutString = OutString & Chr(CurrentCharNum)
                I = I + 3
            Else
               OutString = OutString & Mid(StringToProcess, I, 1)
            End If
        Next I
        ProcessTilde = OutString
        StringToProcess = ""
End Function


Public Function ProcessEAN5AddOn(EAN5AddOn as string) as string
    If Len(EAN5AddOn) = 5 Then
        EANAddOnToPrint = ""
        'Get the check digit for the add on
        Factor = 3
        WeightedTotal = 0
        For I = Len(EAN5AddOn) To 1 Step -1
        'Get the value of each number starting at the end
        CurrentCharNum = Mid(EAN5AddOn, I, 1)
        'Multiply by the weighting factor which is 3,9,3,9.
        'and add the sum together
        If Factor = 3 Then WeightedTotal = WeightedTotal + CurrentCharNum * 3
        If Factor = 1 Then WeightedTotal = WeightedTotal + CurrentCharNum * 9
        'Change factor for next calculation
        Factor = 4 - Factor
        Next I
        'Find the CheckDigit by extracting the right-most number from WeightedTotal
        CheckDigit = Val(Right$(WeightedTotal, 1))
        'Encode the add-on CheckDigit into the number sets
        'by using variable parity between character sets A and B
        Select Case CheckDigit
        Case 0
        Encoding = "BBAAA"
        Case 1
        Encoding = "BABAA"
        Case 2
        Encoding = "BAABA"
        Case 3
        Encoding = "BAAAB"
        Case 4
        Encoding = "ABBAA"
        Case 5
        Encoding = "AABBA"
        Case 6
        Encoding = "AAABB"
        Case 7
        Encoding = "ABABA"
        Case 8
        Encoding = "ABAAB"
        Case 9
        Encoding = "AABAB"
        End Select
        'Determine the characters to print for proper barcoding
        For I = 1 To Len(EAN5AddOn)
        'Get the value of each number encoded with variable parity
        CurrentChar = Mid(EAN5AddOn, I, 1)
        CurrentEncoding = Mid(Encoding, I, 1)
        'Print different barcodes according to the location of the CurrentChar and CurrentEncoding
        Select Case CurrentEncoding
        Case "A"
             If CurrentChar = "0" Then EANAddOnToPrint = EANAddOnToPrint & Chr(34)
             If CurrentChar = "1" Then EANAddOnToPrint = EANAddOnToPrint & Chr(35)
             If CurrentChar = "2" Then EANAddOnToPrint = EANAddOnToPrint & Chr(36)
             If CurrentChar = "3" Then EANAddOnToPrint = EANAddOnToPrint & Chr(37)
             If CurrentChar = "4" Then EANAddOnToPrint = EANAddOnToPrint & Chr(38)
             If CurrentChar = "5" Then EANAddOnToPrint = EANAddOnToPrint & Chr(44)
             If CurrentChar = "6" Then EANAddOnToPrint = EANAddOnToPrint & Chr(46)
             If CurrentChar = "7" Then EANAddOnToPrint = EANAddOnToPrint & Chr(47)
             If CurrentChar = "8" Then EANAddOnToPrint = EANAddOnToPrint & Chr(58)
             If CurrentChar = "9" Then EANAddOnToPrint = EANAddOnToPrint & Chr(59)
        Case "B"
             If CurrentChar = "0" Then EANAddOnToPrint = EANAddOnToPrint & Chr(122)
             If CurrentChar = "1" Then EANAddOnToPrint = EANAddOnToPrint & Chr(61)
             If CurrentChar = "2" Then EANAddOnToPrint = EANAddOnToPrint & Chr(63)
             If CurrentChar = "3" Then EANAddOnToPrint = EANAddOnToPrint & Chr(64)
             If CurrentChar = "4" Then EANAddOnToPrint = EANAddOnToPrint & Chr(91)
             If CurrentChar = "5" Then EANAddOnToPrint = EANAddOnToPrint & Chr(92)
             If CurrentChar = "6" Then EANAddOnToPrint = EANAddOnToPrint & Chr(93)
             If CurrentChar = "7" Then EANAddOnToPrint = EANAddOnToPrint & Chr(95)
             If CurrentChar = "8" Then EANAddOnToPrint = EANAddOnToPrint & Chr(123)
             If CurrentChar = "9" Then EANAddOnToPrint = EANAddOnToPrint & Chr(125)
        End Select
        'Add in the space & add-on guard pattern
        Select Case I
        Case 1
             EANAddOnToPrint = Chr(43) & EANAddOnToPrint & Chr(33)
             'Print add-on delineators between each add-on character
        Case 2
             EANAddOnToPrint = EANAddOnToPrint & Chr(33)
        Case 3
             EANAddOnToPrint = EANAddOnToPrint & Chr(33)
        Case 4
             EANAddOnToPrint = EANAddOnToPrint & Chr(33)
        Case 5
             EANAddOnToPrint = EANAddOnToPrint
        End Select
        Next I
    End If
    ProcessEAN5AddOn = EANAddOnToPrint
End Function


Public Function ProcessEAN2AddOn(EAN2AddOn as string) as string
    'Process the 2 digit add on
    EANAddOnToPrint = ""
    If Len(EAN2AddOn) = 2 Then
        'Get encoding for add on
        For I = 0 To 99 Step 4
           If Val(EAN2AddOn) = I Then Encoding = "AA"
           If Val(EAN2AddOn) = I + 1 Then Encoding = "AB"
           If Val(EAN2AddOn) = I + 2 Then Encoding = "BA"
           If Val(EAN2AddOn) = I + 3 Then Encoding = "BB"
        Next I
        For I = 1 To Len(EAN2AddOn)
           'Get the value of each number
           'It is encoded with variable parity
           CurrentChar = Mid(EAN2AddOn, I, 1)
           CurrentEncoding = Mid(Encoding, I, 1)
           'Print different barcodes according to the location of the CurrentChar and CurrentEncoding
           Select Case CurrentEncoding
           Case "A"
                If CurrentChar = "0" Then EANAddOnToPrint = EANAddOnToPrint & Chr(34)
                If CurrentChar = "1" Then EANAddOnToPrint = EANAddOnToPrint & Chr(35)
                If CurrentChar = "2" Then EANAddOnToPrint = EANAddOnToPrint & Chr(36)
                If CurrentChar = "3" Then EANAddOnToPrint = EANAddOnToPrint & Chr(37)
                If CurrentChar = "4" Then EANAddOnToPrint = EANAddOnToPrint & Chr(38)
                If CurrentChar = "5" Then EANAddOnToPrint = EANAddOnToPrint & Chr(44)
                If CurrentChar = "6" Then EANAddOnToPrint = EANAddOnToPrint & Chr(46)
                If CurrentChar = "7" Then EANAddOnToPrint = EANAddOnToPrint & Chr(47)
                If CurrentChar = "8" Then EANAddOnToPrint = EANAddOnToPrint & Chr(58)
                If CurrentChar = "9" Then EANAddOnToPrint = EANAddOnToPrint & Chr(59)
           Case "B"
                If CurrentChar = "0" Then EANAddOnToPrint = EANAddOnToPrint & Chr(122)
                If CurrentChar = "1" Then EANAddOnToPrint = EANAddOnToPrint & Chr(61)
                If CurrentChar = "2" Then EANAddOnToPrint = EANAddOnToPrint & Chr(63)
                If CurrentChar = "3" Then EANAddOnToPrint = EANAddOnToPrint & Chr(64)
                If CurrentChar = "4" Then EANAddOnToPrint = EANAddOnToPrint & Chr(91)
                If CurrentChar = "5" Then EANAddOnToPrint = EANAddOnToPrint & Chr(92)
                If CurrentChar = "6" Then EANAddOnToPrint = EANAddOnToPrint & Chr(93)
                If CurrentChar = "7" Then EANAddOnToPrint = EANAddOnToPrint & Chr(95)
                If CurrentChar = "8" Then EANAddOnToPrint = EANAddOnToPrint & Chr(123)
                If CurrentChar = "9" Then EANAddOnToPrint = EANAddOnToPrint & Chr(125)
           End Select
           'Add in the space & add-on guard pattern
           Select Case I
           Case 1
                EANAddOnToPrint = Chr(43) & EANAddOnToPrint & Chr(33)
                'Print add-on delineators between each add-on character
           Case 2
                EANAddOnToPrint = EANAddOnToPrint
           End Select
        Next I
    End If
    ProcessEAN2AddOn = ProcessEAN2AddOn & EANAddOnToPrint
End Function    


'*********************************************************************
'*  VB Functions for IDAutomation Barcode Fonts v2006.2
'*  Copyright, IDAutomation.com, Inc. 2000-2006. All rights reserved.
'*
'*  Visit http://www.idautomation.com/openoffice/ for more
'*  information about the functions in this file.
'*
'*  You may incorporate our Source Code in your application
'*  only if you own a valid license from IDAutomation.com, Inc.
'*  for the associated font and this text and the copyright notices
'*  are not removed from the source code.
'*
'*  Distributing our source code or fonts outside your
'*  organization requires a Developer License.
'*********************************************************************
' Internal Version 2006.2.15
