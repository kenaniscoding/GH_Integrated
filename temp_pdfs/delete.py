#!/usr/bin/env python3
"""
PDF Deletion Script

This script deletes all PDF files in the same directory where this script is located.
"""

import os
import sys

def delete_pdf_files():
    # Get the directory where this script is located
    script_dir = os.path.dirname(os.path.abspath(__file__))
    
    # Change to that directory
    os.chdir(script_dir)
    
    # Count the number of PDF files deleted
    deleted_count = 0
    
    # Get list of all files in the directory
    files = os.listdir(script_dir)
    
    print(f"Searching for PDF files in: {script_dir}")
    
    # Look for PDF files and delete them
    for file in files:
        if file.lower().endswith('.pdf'):
            try:
                file_path = os.path.join(script_dir, file)
                os.remove(file_path)
                print(f"Deleted: {file}")
                deleted_count += 1
            except Exception as e:
                print(f"Error deleting {file}: {e}")
    
    # Print summary
    if deleted_count == 0:
        print("No PDF files found.")
    else:
        print(f"Successfully deleted {deleted_count} PDF file(s).")

if __name__ == "__main__":
    # Ask for confirmation before deleting
    delete_pdf_files()
    print("PDF deletion complete.")