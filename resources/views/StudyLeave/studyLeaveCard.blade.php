 
   
                                <tr>
                                    <td>
                                        <select class="form-select" disabled>
                                            <option value="" @selected(optional($leave)->degree_title === '')>Select</option>
                                            <option value="M.A." @selected(optional($leave)->degree_title === 'MA')>M.A.</option>
                                            <option value="M.Sc" @selected(optional($leave)->degree_title === 'MSc')>M.Sc</option>
                                            <option value="MBA" @selected(optional($leave)->degree_title === 'MBA')>MBA</option>
                                            <option value="M.Phil." @selected(optional($leave)->degree_title === 'MPhil.')>M.Phil.</option>
                                            <option value="M.D." @selected(optional($leave)->degree_title === 'MD')>M.D.</option>
                                            <option value="PhD" @selected(optional($leave)->degree_title === 'PhD')>PhD</option>
                                             <option value="Other" @selected(optional($leave)->degree_title === 'Other')>Other</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="prev_university[]" class="form-control" value="{{ optional($leave)->university_institute }}" readonly></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="align-self-center">from</span>
                                            <input type="date" name="prev_duration_from[]" class="form-control" placeholder="From" value="{{ optional($leave)->study_leave_from }}" readonly>
                                            <span class="align-self-center">to</span>
                                            <input type="date" name="prev_duration_to[]" class="form-control" placeholder="To" value="{{ optional($leave)->study_leave_to }}" readonly>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <select name="prev_with_pay_display" class="form-select" disabled>
                                            <option value="" @selected(optional($leave)->leave_payment_type=== '')>Select</option>
                                            <option value="With Pay" @selected(optional($leave)->leave_payment_type === 'with Pay')>With Pay</option>
                                            <option value="No Pay" @selected(optional($leave)->leave_payment_type === 'without Pay')>No Pay</option>
                                        </select>
                                        
                                    <td class="text-center">
                                        <select name="prev_completed[]" class="form-select" disabled>
                                            <option value="">Select</option>
                                            <option value="Completed" @selected(optional($leave)->study_leave_to && now()->isAfter($leave->study_leave_to))>Completed</option>
                                            <option value="Not Completed" @selected(optional($leave)->study_leave_to && now()->isBefore($leave->study_leave_to))>Not Completed</option>
                                        </select>
                                    </td>      
                                </tr>
                            
