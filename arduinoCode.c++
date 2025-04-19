#include <SPI.h>
#include <MFRC522.h>
#include <LiquidCrystal.h>

#define RST_PIN 9
#define SS_PIN 10

const int greenLED = 7;
const int redLED = 8;
const int buzzerPin = A2;
LiquidCrystal lcd(4, 5, 2, 3, 6, A0); 

MFRC522 mfrc522(SS_PIN, RST_PIN);

void setup() {
    Serial.begin(9600);
    SPI.begin();
    mfrc522.PCD_Init();
    lcd.begin(16, 2);
    delay(100);
    lcd.print("Scan your card");
    
    pinMode(greenLED, OUTPUT);
    pinMode(redLED, OUTPUT);
    pinMode(buzzerPin, OUTPUT);
}

void loop() {
    static unsigned long lastActivity = 0;
    if (millis() - lastActivity > 10000) { 
        lcd.clear();
        lcd.print("Scan your card");
    }


    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
        String cardID = "";
        for (byte i = 0; i < mfrc522.uid.size; i++) {
            if (mfrc522.uid.uidByte[i] < 0x10) cardID += "0";
            cardID += String(mfrc522.uid.uidByte[i], HEX);
        }
        Serial.println(cardID);
        lastActivity = millis();
        
        lcd.clear();
        lcd.print("Card detected:");
        lcd.setCursor(0, 1);
        lcd.print(cardID);
        

        tone(buzzerPin, 1000, 100);
        delay(1000); 
    }

    if (Serial.available() > 0) {
        char signal = Serial.read();
        lastActivity = millis();
        
        if (signal == 'G') {
            digitalWrite(greenLED, HIGH);
            digitalWrite(redLED, LOW);
            lcd.clear();
            lcd.print("Access granted");
            
            tone(buzzerPin, 1000, 100);
            delay(200);
            tone(buzzerPin, 1000, 100);
            
            delay(5000);
            digitalWrite(greenLED, LOW);
        } 
        else if (signal == 'R') {
            digitalWrite(redLED, HIGH);
            digitalWrite(greenLED, LOW);
            lcd.clear();
            lcd.print("Access denied");
            tone(buzzerPin, 1000, 500);
            
            delay(5000);
            digitalWrite(redLED, LOW);
        }
        
        lcd.clear();
        lcd.print("Scan your card");
    }
}
